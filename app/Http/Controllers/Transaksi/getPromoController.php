<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\Member;
use App\Models\Kasir\PromosiLarangan;
use App\Models\Kasir\PromosiProduk;
use App\Models\Produk\Promo;
use App\Models\Transaksi\Transaksi;
use App\Models\Transaksi\TransaksiLog;
use Illuminate\Support\Collection;

class getPromoController extends Controller
{
    private string $noTelepone;
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

    public function __construct($dataRequest)
    {
        $this->noTelepone = $dataRequest->nomor_telepone;

        $this->transaksi = Transaksi::findOrFail($dataRequest->id_transaksi);
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
        if ($this->member && $this->promoMember) {
            $this->checkPromoMember();
        }

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

        if ($subtotal >= $this->promoMember->nominal_min_pembelian &&
            $subtotal <= $this->promoMember->nominal_maks_pembelian) {

            $nilai = $this->promoMember->nilai_potongan;
            $this->potonganMember = $this->promoMember->tipe_potongan === '%'
                ? ($subtotal * ($nilai / 100))
                : floatval($nilai);
        }
    }

    private function checkPromoProduk(): void
    {
        foreach ($this->promoProdukList as $promoDetail) {
            $logItem = $this->transaksiLog->firstWhere('plu', $promoDetail->plu);
            if (!$logItem) continue;

            // Lewatkan jika PLU termasuk terlarang
            if (in_array($logItem->plu, $this->promoPluLarangan)) {
                continue;
            }

            $promoUtama = $this->promoProduk->firstWhere('kode_promo', $promoDetail->kode_promo);
            if (!$promoUtama) continue;

            $qty = $logItem->jumlah;
            $totalNominal = $logItem->total;
            $masukSyarat = false;

            // Cek syarat promo berdasarkan tipe
            if ($promoDetail->tipe === '@') {
                if ($qty >= $promoDetail->qty_min && $qty <= $promoDetail->qty_max) {
                    $masukSyarat = true;
                }
            } elseif ($promoDetail->tipe === '$') {
                if ($totalNominal >= $promoDetail->nominal_min && $totalNominal <= $promoDetail->nominal_max) {
                    $masukSyarat = true;
                }
            }

            if (!$masukSyarat) continue;

            // Hitung potongan
            $potongan = 0;
            if ($promoUtama->tipe_potongan === '%') {
                $potongan = $totalNominal * ($promoUtama->nilai_potongan / 100);
            } else {
                $potongan = floatval($promoUtama->nilai_potongan);
            }

            // Hitung gross: total - potongan
            $gross = $totalNominal - $potongan;

            // Update objek TransaksiLog (jika ada kolom 'gross' di DB)
            $logItem->gross = $gross;
            // $logItem->save(); // Un-comment jika ingin langsung menyimpan ke DB

            // Simpan info potongan
            $this->potonganProduk[] = [
                'kode_promo' => $promoDetail->kode_promo,
                'plu' => $promoDetail->plu,
                'qty' => $qty,
                'harga' => $logItem->harga_jual,
                'total' => $totalNominal,
                'potongan' => $potongan,
                'gross' => $gross,
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
