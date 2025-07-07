<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Produk\Produk;
use App\Models\Transaksi\Transaksi;
use App\Models\Transaksi\TransaksiLog;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function getProdukJual(): JsonResponse
    {
        $data = Produk::with('kategori:id,nama_kategori')->where('bisa_jual', true)
        ->select(['plu', 'nama_produk', 'id_kategori', 'harga_jual'])->get();

        $produk = $data->map(function ($item) {
            return [
                'plu'          => $item->plu,
                'nama_produk'  => $item->nama_produk,
                'harga_jual'   => $item->harga_jual,
                'kategori'     => $item->kategori->nama_kategori ?? '-',
            ];
        });

        return response()->json([
            'pesan' => 'berhasil ambil data produk jual',
            'data'  => compact(['produk'])
        ], 200);
    }

    public function getOrder(): JsonResponse
    {
        $userOrder = new OrderController();
        $order = $userOrder->order();

        return response()->json([
            'messages'  => 'success get list order',
            'data'      => $order
        ], 200);
    }

    public function addItemList(Request $request)
    {
        $newItem = $request->validate([
            'plu'           => 'required|regex:/^\d+$/',
            'id_transaksi'   => 'required|regex:/^\d+$/'
        ]);

        $produk = Produk::with(['kategori'])->where('plu', $newItem['plu'])->first();
        if(!$produk->bisa_jual) {
            return response()->json([
                'errors'    => "PLU tidak bisa dijual!"
            ], 400);
        }

        $log = TransaksiLog::where('plu', $request->plu)->where('id_transaksi', $request->idTransaksi)->first();

        return response()->json([
            'message'   => 'Berhasil tambah item',
            'data'      => compact(['newItem'])
        ], 201);
    }

    private function addItemListNew($newItem)
    {

    }
}
