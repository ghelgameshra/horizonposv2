<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Produk\Produk;
use App\Models\Transaksi\Transaksi;
use App\Models\Transaksi\TransaksiLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private ?Transaksi $order = null;
    private ?Collection $orderLists = null;
    private $user;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->initializeOrder();
    }

    private function initializeOrder(): void
    {
        // Step 1: Cari atau buat order
        $this->order = Transaksi::firstOrCreate(
            ['terima' => 0, 'kasir_id' => $this->user->id, 'tipe_bayar' => null],
            ['tanggal_transaksi' => now()]
        );

        // Step 2: Update tanggal jika bukan hari ini
        if (!Carbon::parse($this->order->tanggal_transaksi)->isToday()) {
            $this->order->update(['tanggal_transaksi' => now()]);
        }

        // Step 3: Ambil log, belum updateTotal di sini
        $this->loadOrderLists();
    }

    public function order(): array
    {
        // Update total terbaru saat dipanggil
        $this->updateTotal($this->order->id);

        // Ambil ulang order & log setelah update
        $this->order = Transaksi::find($this->order->id);
        $this->loadOrderLists();

        return [
            'order'      => $this->order->only(['id', 'terima', 'subtotal', 'total', 'diskon']),
            'orderLists' => $this->orderLists,
        ];
    }

    private function loadOrderLists(): void
    {
        $this->orderLists = TransaksiLog::where('id_transaksi', $this->order->id)->get();
    }

    private function updateTotal(int $orderId): void
    {
        DB::update("
            UPDATE transaksi_log
            SET total = harga_ukuran * jumlah,
                gross = total - potongan
            WHERE id_transaksi = ?
        ", [$orderId]);

        DB::update("
            UPDATE transaksi
                SET
                subtotal = (
                    SELECT COALESCE(SUM(gross), 0)
                    FROM transaksi_log
                    WHERE id_transaksi = ?
                ),
                total = CASE
                    WHEN (
                    SELECT COALESCE(SUM(gross), 0)
                    FROM transaksi_log
                    WHERE id_transaksi = ?
                    ) = 0 THEN 0
                    ELSE subtotal - diskon
                END
                WHERE id = ?;

        ", [$orderId, $orderId, $orderId]);
    }

    public function addQty(int $id, int $amount): void
    {
        $log = TransaksiLog::where('id', $id)->first();
        $produk = Produk::where('plu', $log->plu)->first();
        $produk->stok = $produk->stok - $amount;
        $produk->save();

        $stockAwal = $produk->stok + $amount;
        $stockAkhir = $produk->stok;

        $log->informasi_stok = "stock_awal: $stockAwal|stock_akhir: $stockAkhir";
        $log->jumlah += $amount;
        $log->save();

        $this->updateTotal($id);
    }

    public function reduceQty(int $id, int $amount): void
    {
        $log = TransaksiLog::where('id', $id)->first();
        $produk = Produk::where('plu', $log->plu)->first();
        $produk->stok = $produk->stok + $amount;
        $produk->save();

        $stockAwal = $produk->stok - $amount;
        $stockAkhir = $produk->stok;

        $log->informasi_stok = "stock_awal: $stockAwal|stock_akhir: $stockAkhir";
        $log->jumlah -= $amount;
        $log->save();

        if($log->jumlah <= 0) {
            $log->delete();
        }

        $this->updateTotal($id);
    }

    public function setFileName(int $id, string $filename) : void
    {
        $log = TransaksiLog::where('id', $id)->first();
        $log->namafile = strtoupper($filename);
        $log->save();
    }

    public function setSize(int $id, string $size): void
    {
        $log = TransaksiLog::find($id);

        if (!$log) {
            throw new \Exception("Data transaksi_log dengan ID $id tidak ditemukan.");
        }

        $ukuranInput = strtoupper($size);
        $hargaUkuran = 0;

        if (in_array($log->satuan, ['LUAS', 'KELILING'])) {
            $ukuran = explode('X', $ukuranInput);

            if (count($ukuran) === 2 && is_numeric($ukuran[0]) && is_numeric($ukuran[1])) {
                $panjang = $ukuran[0] / 100; // cm ke meter
                $lebar   = $ukuran[1] / 100;

                $hargaUkuran = match ($log->satuan) {
                    'LUAS'      => $panjang * $lebar * $log->harga_jual,
                    'KELILING'  => ($panjang + $lebar) * 2 * $log->harga_jual,
                };
            }
        }

        $log->ukuran        = $ukuranInput;
        $log->harga_ukuran  = $hargaUkuran;
        $log->total         = $log->jumlah * $hargaUkuran;
        $log->gross         = $log->jumlah * $hargaUkuran;

        $log->save();

        $this->updateTotal($id);
    }
}
