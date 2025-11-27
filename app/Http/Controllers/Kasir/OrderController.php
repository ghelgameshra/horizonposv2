<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Produk\Produk;
use App\Models\Transaksi\Transaksi;
use App\Models\Transaksi\TransaksiLog;
use App\Services\Transaksi\HitungTotalService;
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
    protected HitungTotalService $hitungTotal;

    public function __construct() {
        $this->user = Auth::user();
        $this->initializeOrder();
    }

    /**
     * Inisialisasi order aktif untuk kasir login
     */
    private function initializeOrder(): void
    {
        $this->order = Transaksi::firstOrCreate(
            ['terima' => 0, 'kasir_id' => $this->user->id, 'tipe_bayar' => null],
            ['tanggal_transaksi' => now()]
        );

        if (!Carbon::parse($this->order->tanggal_transaksi)->isToday()) {
            $this->order->update(['tanggal_transaksi' => now()]);
        }

        $this->loadOrderLists();
    }

    /**
     * Ambil data order dan detail log
     */
    public function order(): array
    {
        $this->updateTotal($this->order->id);

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
                SET subtotal = (
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

    /**
     * Tambah jumlah item dalam transaksi log
     */
    public function addQty(int $id, int $amount): void
    {
        $log = TransaksiLog::find($id);
        $produk = Produk::where('plu', $log->plu)->first();

        $produk->stok -= $amount;
        $produk->save();

        $stockAwal = $produk->stok + $amount;
        $stockAkhir = $produk->stok;

        $log->informasi_stok = "stock_awal: $stockAwal|stock_akhir: $stockAkhir";
        $log->jumlah += $amount;
        $log->save();

        $this->updateTotal($log->id_transaksi);
    }

    /**
     * Kurangi jumlah item dalam transaksi log
     */
    public function reduceQty(int $id, int $amount): void
    {
        $log = TransaksiLog::find($id);
        $produk = Produk::where('plu', $log->plu)->first();

        $produk->stok += $amount;
        $produk->save();

        $stockAwal = $produk->stok - $amount;
        $stockAkhir = $produk->stok;

        $log->informasi_stok = "stock_awal: $stockAwal|stock_akhir: $stockAkhir";
        $log->jumlah -= $amount;

        if ($log->jumlah <= 0) {
            $log->delete();
        } else {
            $log->save();
        }

        $this->updateTotal($log->id_transaksi);
    }

    /**
     * Set nama file pada transaksi log
     */
    public function setFileName(int $id, string $filename): void
    {
        $log = TransaksiLog::find($id);
        if (!$log) {
            throw new \Exception("Data transaksi_log dengan ID $id tidak ditemukan.");
        }

        $log->namafile = strtoupper($filename);
        $log->save();
    }

    /**
     * Hitung ulang harga ukuran berdasarkan satuan jual
     * menggunakan HitungTotalService
     */
    public function setSize(int $id, string $size): void
    {
        $log = TransaksiLog::find($id);
        if (!$log) {
            throw new \Exception("Data transaksi_log dengan ID $id tidak ditemukan.");
        }

        // Gunakan service untuk menghitung total berdasarkan satuan
        $result = $this->hitungTotal->hitung([
            'ukuran' => $size,
            'satuan' => strtolower($log->satuan),
            'harga'  => $log->harga_jual,
            'qty'    => $log->jumlah,
        ]);

        $log->ukuran        = strtoupper($size);
        $log->harga_ukuran  = $result['nilai'] * $log->harga_jual;
        $log->total         = $result['subtotal'];
        $log->gross         = $result['subtotal'];
        $log->save();

        $this->updateTotal($log->id_transaksi);
    }
}
