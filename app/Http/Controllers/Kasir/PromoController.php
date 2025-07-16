<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\Member;
use App\Models\Kasir\PromosiLarangan;
use App\Models\Kasir\PromosiProduk;
use App\Models\Produk\Promo;
use App\Models\Transaksi\Transaksi;
use App\Models\Transaksi\TransaksiLog;
use Illuminate\Support\Collection;

class PromoController extends Controller
{
    private $noTelepone = null;
    private Transaksi $transaksi;
    private Collection $transaksiLog;
    private Member|null $member = null;
    private Promo|null $promoMember = null;
    private Collection $promoProduk;
    private Collection $promoProdukList;

    private array $pluList = [];
    private array $kodePromoProduk = [];
    private array $promoPluLarangan = [];

    private float $potonganMember = 0;
    private array $potonganProduk = [];

    public function __construct(string $telepone, string $idTransaksi)
    {
        $this->noTelepone = $telepone;

        $this->transaksi = Transaksi::findOrFail($idTransaksi);
        $this->transaksiLog = TransaksiLog::where('id_transaksi', $this->transaksi->id)->get();
        $this->pluList = $this->transaksiLog->pluck('plu')->toArray();

        $this->member = Member::where('telepone', $this->noTelepone)->first();

        $this->promoProduk = Promo::where('tipe_promo', 'PRODUK')
            ->where('status_promo', true)
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->get();

        $this->kodePromoProduk = $this->promoProduk->pluck('kode_promo')->toArray();

        $this->promoProdukList = PromosiProduk::whereIn('kode_promo', $this->kodePromoProduk)
            ->whereIn('plu', $this->pluList)
            ->get();

        $this->promoMember = Promo::where('promo_member', true)
            ->where('tipe_promo', 'MEMBER')
            ->where('status_promo', true)
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->first();

        $this->promoPluLarangan = PromosiLarangan::whereIn('kode_promo', array_merge(
                $this->kodePromoProduk,
                [$this->promoMember?->kode_promo]
            ))
            ->pluck('plu')
            ->toArray();
    }

    public function checkPromo()
    {
        $this->checkPromoMember();
        $this->checkPromoProduk();
        $this->updateGross();

        return [
            'telepone' => $this->noTelepone,
            'transaksi' => $this->transaksi,
            'transaksi_log' => $this->transaksiLog,
            'plu_list' => $this->pluList,
            'member' => $this->member,
            'promo_member' => $this->promoMember,
            'promo_produk' => $this->promoProduk,
            'promo_produk_list' => $this->promoProdukList,
            'potongan_member' => $this->potonganMember,
            'potongan_produk' => $this->potonganProduk,
        ];
    }

    private function checkPromoMember(): void
    {
        // Cek jika ada PLU terlarang
        foreach ($this->pluList as $plu) {
            if (in_array($plu, $this->promoPluLarangan)) {
                return;
            }
        }

        $subtotal = $this->transaksi->subtotal;

        if ($this->promoMember && $subtotal >= $this->promoMember->nominal_min_pembelian &&
            $subtotal <= $this->promoMember->nominal_maks_pembelian && $this->member) {

            $nilai = $this->promoMember->nilai_potongan;
            $this->potonganMember = $this->promoMember->tipe_potongan === '%'
                ? ($subtotal * ($nilai / 100))
                : floatval($nilai);
        }

        $this->transaksi->diskon = $this->potonganMember;
        $this->transaksi->nama_customer   = $this->member?->nama_lengkap;
        $this->transaksi->id_member   = $this->member?->id;
        $this->transaksi->kode_promo     = $this->member ? $this->promoMember?->kode_promo : null;
        $this->transaksi->nomor_telepone = $this->noTelepone?: null;
        $this->transaksi->save();
    }

    private function checkPromoProduk(): void
    {
        foreach ($this->promoProdukList as $promoDetail) {
            $logItem = $this->transaksiLog->firstWhere('plu', $promoDetail->plu);
            if (!$logItem) continue;

            // Lewatkan jika PLU termasuk larangan
            if (in_array($logItem->plu, $this->promoPluLarangan)) {
                continue;
            }

            $promoUtama = $this->promoProduk->firstWhere('kode_promo', $promoDetail->kode_promo);
            if (!$promoUtama) continue;

            $qty = $logItem->jumlah;
            $hargaSatuan = $logItem->harga_ukuran > 0 ? $logItem->harga_ukuran : $logItem->harga_jual;
            $totalNominal = $qty * $hargaSatuan;

            $masukSyarat = match($promoDetail->tipe) {
                '@' => $qty >= $promoDetail->qty_min && $qty <= $promoDetail->qty_max,
                '$' => $totalNominal >= $promoDetail->nominal_min && $totalNominal <= $promoDetail->nominal_max,
                default => false,
            };

            if (!$masukSyarat) continue;

            // Hitung potongan
            $potongan = $promoUtama->tipe_potongan === '%'
                ? $totalNominal * ($promoUtama->nilai_potongan / 100)
                : floatval($promoUtama->nilai_potongan);

            $logItem->potongan = $potongan;
            $logItem->gross = $totalNominal - $potongan;
            $logItem->save();

            // Simpan detail potongan
            $this->potonganProduk[] = [
                'kode_promo' => $promoDetail->kode_promo,
                'plu'        => $promoDetail->plu,
                'qty'        => $qty,
                'harga'      => $hargaSatuan,
                'total'      => $totalNominal,
                'potongan'   => $potongan,
                'gross'      => $totalNominal - $potongan,
            ];
        }
    }


    private function updateGross(): void
    {
        foreach ($this->transaksiLog as $log) {
            // Cari potongan berdasarkan PLU
            $potongan = collect($this->potonganProduk)
                ->firstWhere('plu', $log->plu);

            if ($potongan) {
                $log->kode_promo = $potongan['kode_promo'];
                $log->gross = $potongan['gross']; // Gross sudah dihitung sebelumnya
            } else {
                $log->kode_promo = null;
                $log->gross = $log->total; // Tidak ada potongan, gross = total
            }

            $log->save(); // Simpan perubahan
        }
    }
}
