<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\ConstApp;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    public function data(): JsonResponse
    {
        $jumlahAntrian = ConstApp::where('key', 'ANTR')->first();
        $antrianAktif = ConstApp::where('key', 'ANTP')->first();

        if (Carbon::parse($jumlahAntrian->periode)->lt(Carbon::today())) {
            $jumlahAntrian->update(['periode' => now(),'docno' => 0]);
            $antrianAktif->update(['periode' => now(),'docno' => 0]);
        }

        return response()->json([
            'data'  => [
                'jumlahAntrian'     => $jumlahAntrian->docno,
                'antrianAktif'      => $antrianAktif->docno,
                'panggilAntrian'    => $antrianAktif->docno,
            ],
        ]);
    }

    public function create(): JsonResponse
    {
        $antrianBaru = ConstApp::where('key', 'ANTR')->first();
        $antrianBaru->update(['docno' => $antrianBaru->docno+1]);

        return response()->json([
            'pesan' => "No antrian $antrianBaru->docno berhasil dibuat"
        ], 201);
    }

    public function update(): JsonResponse
    {
        $antrianBaru = ConstApp::where('key', 'ANTP')->first();
        $antrianLanjut = ConstApp::where('key', 'ANTR')->first();

        if($antrianBaru->docno === $antrianLanjut->docno){
            throw new HttpResponseException(response([
                'message' => 'Belum ada nomor antrian baru'
            ], 422));
        }

        $antrianBaru->docno += 1;
        $antrianBaru->save();

        return response()->json([
            'pesan' => "berhasil panggil antrian selanjunya"
        ], 201);
    }

    public function repeat(): JsonResponse
    {
        $antrianBaru = ConstApp::where('key', 'ANTP')->first();
        $antrian = $antrianBaru->docno;

        if($antrian === 0){
            throw new HttpResponseException(response([
                'message' => 'Belum ada nomor antrian'
            ], 422));
        }

        $nomorMeja = $this->cekNomorMeja();

        return response()->json([
            'pesan' => "Nomor antrian $antrian silahkan menuju ke meja desainer nomor $nomorMeja"
        ], 201);
    }

    private function cekNomorMeja(): int
    {
        if(!Auth::user()->nomor_meja){
            throw new HttpResponseException(response([
                'message' => 'Nomor meja user belum disetting'
            ], 403));
        }

        return Auth::user()->nomor_meja;
    }

}
