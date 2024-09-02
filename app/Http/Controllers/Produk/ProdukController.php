<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use App\Imports\ProdukImport;
use App\Models\Produk\Produk;
use Illuminate\Http\Request;
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

        return response()->json([
            'pesan' => "berhasil tambah data produk"
        ], 200);
    }

    public function get()
    {
        $produk = DB::table('produk AS p')->join('ref_kategori AS k', 'p.id_kategori', 'k.id')
        ->orderBy('created_at', 'desc')->select(['p.plu', 'p.nama_produk', 'k.nama_kategori', 'p.harga_jual', 'p.stok', 'p.plu_aktif', 'p.jual_minus'])->get();
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
}
