<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use App\Http\Requests\Insert\ProdukInsertRequest;
use App\Http\Requests\Update\UpdateProdukRequest;
use App\Imports\ProdukImport;
use App\Models\Produk\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProdukController extends Controller
{
    public function upsert(Request $request)
    {
        $request->validate([
            'file_produk'   => ['required', 'file', 'mimes:csv,xlsx', 'max:10240']
        ]);

        Excel::import(new ProdukImport, $request->file('file_produk'));
        DB::table('produk')->update([
            'addid' => env('DB_USERNAME') . "@" . $request->ip() . ':' . Auth::user()->email
        ]);

        return response()->json([
            'pesan' => "berhasil tambah data produk"
        ], 200);
    }

    public function get()
    {
        $produk = DB::table('produk AS p')->join('ref_kategori AS k', 'p.id_kategori', 'k.id')
        ->orderBy('p.created_at', 'desc')->select(['p.plu', 'p.nama_produk', 'k.nama_kategori', 'p.harga_jual', 'p.stok', 'p.plu_aktif', 'p.jual_minus', 'p.bisa_jual'])->get();
        return response()->json([
            'data' => $produk
        ], 200);
    }

    public function destroy(Request $request){
        Produk::where('plu', $request->plu)->delete();
        return response()->json([
            'pesan' => "berhasil hapus data PLU $request->plu"
        ], 200);
    }

    public function insert(ProdukInsertRequest $request){
        $data = $request->validated();

        $lastProduk = DB::table('produk')->orderBy('addno', 'desc')->first();
        $lastAddNo = $lastProduk ? $lastProduk->addno + 1 : 1;

        $data['addid']          = env('DB_USERNAME') . "@" . $request->ip() . ':' . Auth::user()->email;
        $data['nama_produk']    = strtoupper($data['nama_produk']);
        $data['plu']            = now()->format('ymd') . str_pad($lastAddNo, 4, '0', STR_PAD_LEFT);
        $data['addno']          = $lastAddNo;

        Produk::create($data);

        return response()->json([
            'pesan' => 'data produk berhasil ditambah'
        ], 200);
    }

    public function activateStatus($plu, Request $request): JsonResponse
    {
        $data = Produk::where('plu', $plu)->first();
        $status = !$data->plu_aktif ? 'diaktifkan' : 'dinonaktifkan dan tidak bisa dijual';

        $data->update([
            'plu_aktif' => !$data->plu_aktif ? true : false,
            'updid'     => env('DB_USERNAME') . "@" . $request->ip() . ':' . Auth::user()->email
        ]);

        $this->setStatusBisaJual($data);

        return response()->json([
            'pesan' => "PLU $plu $status"
        ], 200);
    }

    public function activateStatusJual($plu, Request $request): JsonResponse
    {
        $data = Produk::where('plu', $plu)->first();

        // Menentukan status jual minus dan pesan terkait
        $isJualMinus = !$data->jual_minus;
        $status = $isJualMinus ? "bisa jual minus diaktifkan. PLU $plu bisa dijual" : "bisa jual minus dinonaktifkan";

        // Update data produk
        $data->update([
            'jual_minus'    => $isJualMinus,
            'updid'         => env('DB_USERNAME') . "@" . $request->ip() . ':' . Auth::user()->email
        ]);

        // Menentukan pesan berdasarkan stok dan jual minus
        if (!$isJualMinus && $data->stok <= 0) {
            $bisaJual = "PLU tidak bisa dijual karena stok tidak mencukupi";
        } elseif ($data->stok > 0) {
            $bisaJual = "PLU bisa dijual karena stok lebih dari 0";
        } else {
            $bisaJual = '';
        }

        // Memperbarui status bisa jual
        $this->setStatusBisaJual($data);

        return response()->json([
            'pesan' => "PLU $plu $status. $bisaJual",
        ], 200);
    }


    private function setStatusBisaJual($data): void
    {
        $data->bisa_jual = $data->plu_aktif && ($data->jual_minus || $data->stok > 0);
        $data->save();
    }

    public function data(String $plu): JsonResponse
    {
        $produk = Produk::with('kategori')->where('plu', $plu)->first();
        return response()->json([
            'pesan' => 'berhasil ambil detail produk',
            'data'  => [
                'produk'    => $produk
            ]
        ]);
    }

    public function update(String $plu, UpdateProdukRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['nama_produk']  = strtoupper($data['nama_produk']);
        $data['updid'] = env('DB_USERNAME') . "@" . $request->ip() . ':' . Auth::user()->email;

        $produk = Produk::where('plu', $plu)->first();
        $produk->update($data);

        return response()->json([
            'pesan' => 'berhasil ambil detail produk',
        ]);
    }
}
