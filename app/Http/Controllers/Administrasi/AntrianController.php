<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Struk\AntrianStrukController;
use App\Models\Administrasi\Antrian;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    public function data(): JsonResponse
    {
        $jumlahAntrianDesain = Antrian::where('key', 'ATRD')->first();
        $antrianDesainTerpanggil = Antrian::where('key', 'ATPD')->first();

        $jumlahAntrianCetak = Antrian::where('key', 'ATRS')->first();
        $antrianCetakTerpanggil = Antrian::where('key', 'ATPS')->first();

        if ($jumlahAntrianDesain->periode !== now()->format('Y-m-d') || $jumlahAntrianCetak->periode !== now()->format('Y-m-d')) {
            DB::table('antrian')->update([
                'periode' => now(),
                'docno' => 0,
                'meja'  => 0
            ]);
        }

        $nomorMeja = DB::table('users')->whereNotNull('nomor_meja')
        ->select(['nomor_meja'])->get();

        return response()->json([
            'data'  => [
                'jumlahAntrianDesain'       => $jumlahAntrianDesain->docno,
                'jumlahAntrianCetak'        => $jumlahAntrianCetak->docno,
                'antrianDesainTerpanggil'   => $antrianDesainTerpanggil->docno,
                'antrianCetakTerpanggil'    => $antrianCetakTerpanggil->docno,
                'nomorMeja'                 => $nomorMeja,
            ],
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $antrianBaru = Antrian::where('key', $request->tipe_antrian)->first();
        $antrianBaru->update(['docno' => $antrianBaru->docno+1]);

        $label = new AntrianStrukController($antrianBaru->docno, $antrianBaru->keterangan);
        $label->print();

        return response()->json([
            'pesan' => "$antrianBaru->keterangan no $antrianBaru->docno berhasil dibuat"
        ], 201);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'tipe_antrian' => ['required', 'string'],
            'nomor_meja' => ['required', 'string'],
        ]);
        $noAntrian = Antrian::where('key', $request->tipe_antrian)->first();
        $reference = '';

        if($request->tipe_antrian === 'ATPD'){
            $reference = DB::table('antrian')->where('key', 'ATRD')->first();
        }

        if($request->tipe_antrian === 'ATPS'){
            $reference = DB::table('antrian')->where('key', 'ATRS')->first();
        }

        if($noAntrian->docno + 1 > $reference->docno){
            throw new HttpResponseException(response([
                'message' => 'belum ada antrian baru'
            ], 422));
        }

        $kalimat = $noAntrian->pengucapan;
        $kalimat = str_replace(['{noAntri}'], $noAntrian->docno + 1, $kalimat);
        $kalimat = str_replace(['{noMeja}'], $request->nomor_meja, $kalimat);

        $noAntrian->docno += 1;
        $noAntrian->meja = $request->nomor_meja;
        $noAntrian->save();

        return response()->json([
            'pesan'     => "berhasil panggil antrian selanjunya",
            'kalimat'   => $kalimat
        ], 201);
    }

    public function repeat(Request $request): JsonResponse
    {
        $request->validate(['jenis_antrian' => ['required', 'string']]);
        $noAntrian = Antrian::where('key', $request->jenis_antrian)->first();

        if($request->ganti_meja){
            $noAntrian->meja = $request->ganti_meja;
            $noAntrian->save();
        }

        $kalimat = $noAntrian->pengucapan;
        $kalimat = str_replace(['{noAntri}'], $noAntrian->docno, $kalimat);
        $kalimat = str_replace(['{noMeja}'], $noAntrian->meja, $kalimat);

        if($noAntrian->docno === 0 || $noAntrian->meja === 0){
            throw new HttpResponseException(response([
                'message' => 'Belum ada nomor antrian'
            ], 422));
        }

        return response()->json([
            'pesan'     => "berhasil panggil ulang antrian",
            'kalimat'   => $kalimat
        ], 201);
    }

}
