<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Kasir\OrderController;
use App\Http\Controllers\Kasir\PromoController;
use App\Models\Produk\Produk;
use App\Models\Transaksi\TransaksiLog;
use App\Services\Transaksi\HitungTotalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    private HitungTotalService $hitungService;

    public function __construct(HitungTotalService $hitungService)
    {
        $this->hitungService = $hitungService;
    }

    public function getProdukJual(): JsonResponse
    {
        $produk = Produk::with('kategori:id,nama_kategori')
            ->where('bisa_jual', true)
            ->select(['plu', 'nama_produk', 'id_kategori', 'harga_jual'])
            ->get()
            ->map(function ($item) {
                return [
                    'plu'         => $item->plu,
                    'nama_produk' => $item->nama_produk,
                    'harga_jual'  => $item->harga_jual,
                    'kategori'    => $item->kategori->nama_kategori ?? '-',
                ];
            });

        return response()->json([
            'pesan' => 'berhasil ambil data produk jual',
            'data'  => compact('produk')
        ], 200);
    }

    public function getOrder(): JsonResponse
    {
        $orderController = new OrderController();
        $order = $orderController->order();

        $satuan = DB::table('ref_satuan')
            ->select('nama_satuan', 'input_namafile', 'input_ukuran')
            ->get();

        $this->applyPromo($order['order']['id']);

        return response()->json([
            'messages' => 'success get list order',
            'data'     => compact('order', 'satuan')
        ], 200);
    }

    public function addItemList(Request $request): JsonResponse
    {
        $newItem = $request->validate([
            'plu'           => 'required|regex:/^\d+$/',
            'id_transaksi'  => 'required|regex:/^\d+$/'
        ]);

        $produk = Produk::with('kategori')->where('plu', $newItem['plu'])->first();

        if (!$produk || !$produk->bisa_jual) {
            return response()->json([
                'message' => 'PLU tidak ditemukan atau tidak bisa dijual!'
            ], 400);
        }

        $this->addItemOrder($produk, (int)$newItem['id_transaksi']);

        $this->applyPromo((int)$newItem['id_transaksi']);

        $orderController = new OrderController();
        $order = $orderController->order();

        return response()->json([
            'message' => 'Berhasil tambah item',
            'data'    => compact('newItem', 'order')
        ], 201);
    }

    private function addItemOrder(Produk $produk, int $idTransaksi): void
    {
        $item = TransaksiLog::with('refSatuan')
            ->where('id_transaksi', $idTransaksi)
            ->where('plu', $produk->plu)
            ->first();

        $shouldCreateNew = !$item || $item->refSatuan->input_namafile;
        $shouldFinish = in_array($produk->kategori->nama_kategori, ["JASA", "FINISHING"]) ? 'SELESAI' : 'DALAM ANTRIAN';

        if ($shouldCreateNew) {
            $item = TransaksiLog::create([
                'id_transaksi'  => $idTransaksi,
                'plu'           => $produk->plu,
                'jumlah'        => 0,
                'nama_produk'   => $produk->nama_produk,
                'id_kategori'   => $produk->kategori->id ?? null,
                'harga_jual'    => $produk->harga_jual,
                'harga_ukuran'  => $produk->jenis_ukuran === 'PCS' ? $produk->harga_jual : 0,
                'satuan'        => $produk->satuan,
                'status_order'  => $shouldFinish,
            ]);

            app(OrderController::class)->addQty($item->id, 1);
        } else {
            $item->increment('jumlah');
        }
    }

    public function removeItemOrder(int $id): JsonResponse
    {
        $log = TransaksiLog::where('id', $id)->first();
        $log->delete();

        $orderController = new OrderController();
        $order = $orderController->order();

        $produk = Produk::where('plu', $log->plu)->first();
        $produk->stok += $log->jumlah;
        $produk->save();

        return response()->json([
            'message' => "Success remove item $id",
            'data'    => compact('order')
        ]);
    }

    public function addQty(int $id, Request $request): JsonResponse
    {
        $qty = $request->qty ?? 1;

        $orderController = new OrderController();
        $orderController->addQty($id, $qty);

        $log = TransaksiLog::find($id);
        if ($log) {
            $this->applyPromo((int)$log->id_transaksi);
        }

        $order = $orderController->order();

        return response()->json([
            'message' => "Success add qty item",
            'data'    => compact('order')
        ]);
    }

    public function reduceQty(int $id, Request $request): JsonResponse
    {
        $qty = $request->qty ?? 1;

        $orderController = new OrderController();
        $orderController->reduceQty($id, $qty);

        $log = TransaksiLog::find($id);
        if ($log) {
            $this->applyPromo((int)$log->id_transaksi);
        }

        $order = $orderController->order();

        return response()->json([
            'message' => "Success reduce qty item",
            'data'    => compact('order')
        ]);
    }

    public function setFileName(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'filename' => 'string|min:3|max:50'
        ]);

        $orderController = new OrderController();
        $orderController->setFileName($id, $data['filename']);

        return response()->json([
            'message' => "Success set filename",
        ]);
    }

    /**
     * ✅ Refactor fungsi hitung ukuran pakai HitungTotalService
     */
    public function setSize(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'size' => 'string|min:1|max:50'
        ]);

        $log = TransaksiLog::find($id);
        if (!$log) {
            return response()->json(['message' => 'Data transaksi tidak ditemukan'], 404);
        }

        $ukuranInput = strtoupper(trim($data['size']));

        // 🔹 Gunakan service untuk hitung total berdasarkan satuan
        $hasil = $this->hitungService->hitung([
            'ukuran' => $ukuranInput,
            'satuan' => strtolower($log->satuan),
            'harga'  => $log->harga_jual,
            'qty'    => $log->jumlah ?: 1,
        ]);

        // 🔹 Simpan hasil hitungan
        $log->ukuran        = $ukuranInput;
        $log->harga_ukuran  = $hasil['nilai'] * $log->harga_jual;
        $log->total         = $hasil['subtotal'];
        $log->gross         = $hasil['subtotal'];
        $log->save();

        // Update total transaksi utama
        $orderController = new OrderController();
        $orderController->order();

        $order = $orderController->order();

        return response()->json([
            'message' => "Success set size",
            'data'    => compact('order')
        ]);
    }

    /**
     * Jalankan promo
     */
    private function applyPromo(int $idTransaksi): void
    {
        $promo = new PromoController('', $idTransaksi);
        $promo->checkPromo();
    }
}
