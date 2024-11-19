<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Insert\RekeningInsertRequest;
use App\Models\Administrasi\Rekening;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RekeningController extends Controller
{
    public function data(): JsonResponse
    {
        $rekening = DB::table('ref_rekening')
        ->select(['nama_bank', 'nomor_rekening', 'nama_pemilik', 'default'])->get();

        return response()->json([
            'pesan' => 'Berhasil ambil daftar rekening',
            'data'  => [
                'rekening'  => $rekening
            ]
        ]);
    }

    public function defaultRekening(): JsonResponse
    {
        $rekening = DB::table('ref_rekening')->where('default', true)
        ->select(['nama_bank', 'nomor_rekening', 'nama_pemilik'])->get();

        if($rekening->count() !== 1){
            throw new HttpResponseException(response([
                'message' => 'Tidak ada setting rekening default. silahkan setting default rekening'
            ], 422));
        }

        return response()->json([
            'pesan' => 'Berhasil ambil data rekening',
            'data'  => [
                'rekening'  => $rekening->first()
            ]
        ]);
    }

    public function store(RekeningInsertRequest $request): JsonResponse
    {
        $user = env('DB_USERNAME') . "@" . $request->ip() . ':' . Auth::user()->email;

        $data = $request->validated();
        $data['nama_bank'] = strtoupper($data['nama_bank']);
        $data['nama_pemilik'] = strtoupper($data['nama_pemilik']);
        $data['addid'] = $user;
        $data['updid'] = $user;

        if($data['default']){
            DB::table('ref_rekening')->update([
                'default' => false
            ]);
        }

        try {
            Rekening::create($data);
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                'message' => $th->getMessage()
            ], 422));
        }

        return response()->json([
            'pesan' => 'Berhasil tambah data rekening'
        ]);
    }

    public function destroy(String $nomorRekening): JsonResponse
    {
        $rekening = Rekening::where('nomor_rekening', $nomorRekening)->first();
        $rekening->delete();

        return response()->json([
            'pesan' => 'Berhasil hapus data rekening'
        ]);
    }

    public function update(String $nomorRekening): JsonResponse
    {
        DB::table('ref_rekening')->update([
            'default' => false
        ]);

        $rekening = Rekening::where('nomor_rekening', $nomorRekening)->first();
        $rekening->update(['default' => true]);

        return response()->json([
            'pesan' => 'Berhasil ubah default rekening'
        ]);
    }
}
