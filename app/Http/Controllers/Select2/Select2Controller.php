<?php

namespace App\Http\Controllers\Select2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Select2Controller extends Controller
{
    public function select2JenisUkuran(Request $request)
    {
        $searchTerm = $request->input('search', '');

        // Ambil data dari tabel guru dan format hasilnya
        $data = DB::table('ref_jenis_ukuran')
            ->where('nama_jenis', 'LIKE', "%{$searchTerm}%")
            ->select('id', 'nama_jenis')
            ->get()
            ->map(function ($data) {
                return [
                    'id' => $data->nama_jenis,
                    'text' => $data->nama_jenis,
                ];
            });

        // Kembalikan respons dalam format JSON
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function select2JenisKategori(Request $request)
    {
        $searchTerm = $request->input('search', '');

        // Ambil data dari tabel guru dan format hasilnya
        $data = DB::table('ref_kategori')
            ->where('nama_kategori', 'LIKE', "%{$searchTerm}%")
            ->select('id', 'nama_kategori')
            ->get()
            ->map(function ($data) {
                return [
                    'id' => $data->id,
                    'text' => $data->nama_kategori,
                ];
            });

        // Kembalikan respons dalam format JSON
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function select2JenisSatuan(Request $request)
    {
        $searchTerm = $request->input('search', '');

        // Ambil data dari tabel guru dan format hasilnya
        $data = DB::table('ref_satuan')
            ->where('nama_satuan', 'LIKE', "%{$searchTerm}%")
            ->select('id', 'nama_satuan')
            ->get()
            ->map(function ($data) {
                return [
                    'id' => $data->nama_satuan,
                    'text' => $data->nama_satuan,
                ];
            });

        // Kembalikan respons dalam format JSON
        return response()->json([
            'data' => $data
        ], 200);
    }
}
