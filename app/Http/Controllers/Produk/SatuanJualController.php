<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SatuanJualController extends Controller
{
    public function data(): JsonResponse
    {
        $satuan = DB::table('ref_satuan')->select(["nama_satuan","input_namafile","input_ukuran", "deskripsi", 'id'])->get();
        return response()->json([
            'message'   => 'Success ambil data satuan jual',
            'data'      => compact(['satuan'])
        ]);
    }

    public function updateConfig(int $id, string $config): JsonResponse
    {
        // Daftar kolom yang diizinkan untuk diubah
        $allowedConfigs = ['input_namafile', 'input_ukuran'];

        if (!in_array($config, $allowedConfigs)) {
            return response()->json(['message' => 'Konfigurasi tidak valid.'], 400);
        }

        $satuan = DB::table('ref_satuan')->where('id', $id)->first();

        if (!$satuan) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        // Toggle status
        $status = !$satuan->$config;

        // Hitung nilai baru kedua config
        $new_namafile = $config === 'input_namafile' ? $status : $satuan->input_namafile;
        $new_ukuran   = $config === 'input_ukuran'   ? $status : $satuan->input_ukuran;

        // Bangun deskripsi baru
        $deskripsi = "status satuan jual {$satuan->nama_satuan}, " .
            ($new_namafile ? "bisa input namafile" : "tidak bisa input namafile") . ", " .
            ($new_ukuran ? "bisa input ukuran" : "tidak bisa input ukuran");

        // Update database
        DB::table('ref_satuan')->where('id', $id)->update([
            $config     => $status,
            'deskripsi' => $deskripsi
        ]);

        return response()->json([
            'message' => "Berhasil update $satuan->nama_satuan config '$config' ke " . ($status ? 'aktif' : 'nonaktif')
        ]);
    }


    public function insert(Request $request): JsonResponse
    {
        $request->validate([
            'nama_satuan'   => 'required|string|max:50'
        ]);

        $nama_satuan = strtoupper($request->nama_satuan);
        $nama_satuan = str_replace(' ', '_', $nama_satuan);


        try {
            DB::table("ref_satuan")->insert([
                'nama_satuan'   => $nama_satuan
            ]);
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                'message'   => 'Gagal insert satuan jual'
            ], 422));
        }

        return response()->json([
            'message' => "Berhasil tambah satuan jual $request->nama_satuan"
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $satuan = DB::table('ref_satuan')->where('id', $id)->first();
        $produk = DB::table('produk')->where('satuan', $satuan->nama_satuan)->count();
        $nama_satuan = $satuan->nama_satuan;
        if($produk) {
            throw new HttpResponseException(response([
                'message'   => "Satuan jual $nama_satuan tidak bisa dihapus karena masih digunakan produk"
            ], 422));
        }

        DB::table('ref_satuan')->where('id', $id)->delete();

        return response()->json([
            'message' => "Berhasil hapus satuan jual $nama_satuan"
        ]);
    }

}
