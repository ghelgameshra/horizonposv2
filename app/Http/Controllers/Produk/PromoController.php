<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use App\Models\Produk\Promo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromoController extends Controller
{
    public function get()
    {
        $data = DB::table('promo')->select(['kode_promo', 'promo_member', 'jenis_promo', 'periode_akhir', 'total_penggunaan'])->get();
        return response()->json([
            'pesan'     => 'berhasil ambil data promo',
            'data'      => $data
        ], 200);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = Promo::where('kode_promo', $request->kode_promo)->first();
        $data->delete();

        return response()->json([
            'pesan' => "promo $request->kode_promo berhasil dihapus"
        ], 200);
    }
}
