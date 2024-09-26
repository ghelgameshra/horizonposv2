<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\Pengeluaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    public function insertJenis(Request $request): JsonResponse
    {
        $request->validate([
            'nama_pengeluaran'  => ['required', 'string', 'max:150', 'unique:ref_jenis_pengeluaran,nama_pengeluaran']
        ]);

        DB::table('ref_jenis_pengeluaran')->insert([
            'nama_pengeluaran'  => ucwords($request->nama_pengeluaran),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->json([
            'pesan' => 'Berhasil tambah jenis pengeluaran'
        ], 201);
    }

    public function get(): JsonResponse
    {
        $data = DB::table('pengeluaran')->join('users', 'pengeluaran.user_id', '=', 'users.id')
        ->select(['invno', 'jenis_pengeluaran', 'tanggal_pengeluaran', 'total', 'users.name AS pembuat', 'image'])
        ->get();
        return response()->json([
            'pesan' => 'Berhasil ambil data pengeluaran',
            'data'  => $data
        ]);
    }

    public function insert(Request $request): JsonResponse
    {
        $request->validate([
            'jenis_pengeluaran'     => ['required', 'string', 'max:150'],
            'total'                 => ['required', 'numeric', 'min:1'],
            'tipe_bayar'            => ['required', 'string', 'max:5'],
        ]);

        $data = Pengeluaran::create([
            'tanggal_pengeluaran'   => now(),
            'jenis_pengeluaran'     => $request->jenis_pengeluaran,
            'total'                 => $request->total,
            'tipe_bayar'            => $request->tipe_bayar,
            'user_id'               => Auth::user()->id
        ]);

        $data->invno = "PNG-$data->tipe_bayar" . now()->format('ymd') . str_pad($data->id, 7, '0', STR_PAD_LEFT);
        $data->save();

        return response()->json([
            'pesan' => 'Berhasil tambah pengeluaran'
        ], 201);
    }

    public function uploadRef(Request $request): JsonResponse
    {
        $request->validate([
            'file_ref' => ['required', 'file', 'mimes:png,jpg', 'max:1024']
        ]);


        try {
            $path = $request->file('file_ref')->store('pengeluaran');
            $pengeluaran = Pengeluaran::where('invno', $request->invno)->first();
            $pengeluaran->image = $path;
            $pengeluaran->save();
        } catch (\Throwable $th) {
            throw $th->getMessage();
        }

        return response()->json([
            'pesan' => "Berhasil upload gambar referensi $pengeluaran->invno"
        ], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'invno' => ['required', 'string']
        ]);

        Pengeluaran::where('invno', $request->invno)->delete();

        return response()->json([
            'pesan' => "data $request->invno dihapus"
        ]);
    }
}
