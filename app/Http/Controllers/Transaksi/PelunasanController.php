<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Transaksi\Transaksi;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelunasanController extends Controller
{
    public function get(): JsonResponse
    {
        $data = DB::table('transaksi')->whereNull('tipe_bayar_pelunasan')->whereNotNull('invno')
        ->where('status_order', '!=', 'CANCEL SALES')->get();

        return response()->json([
            'data'  => $data
        ]);
    }

    public function detail(Request $request): JsonResponse
    {
        $data = Transaksi::with(['kasir', 'transaksiLog'])->where('invno', $request->invno)->first();
        return response()->json([
            'pesan' => "berhasil ambil detail transaksi",
            'data'  => $data
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = Transaksi::where('invno', $request->invno)->first();
        $kurangBayar = $data->total - $data->uang_muka;

        if($kurangBayar > $request->terima){
            throw new HttpResponseException(response([
                'message' => 'uang terima tidak bisa lebih kecil dari kurang bayar'
            ], 422));
        }

        if(in_array($request->tipe_bayar_pelunasan, ['TRF']) && $request->terima > $kurangBayar){
            throw new HttpResponseException(response([
                'message' => 'pembayaran transfer harus sesuai nominal'
            ], 422));
        }

        $data->update([
            'terima'    => $data->uang_muka + $request->terima,
            'kembali'   => ($data->uang_muka + $request->terima) - $data->total,
            'tipe_bayar_pelunasan' => $request->tipe_bayar_pelunasan
        ]);

        return response()->json([
            'pesan' => "berhasil buat pelunasan transaksi $data->invno",
        ]);
    }
}
