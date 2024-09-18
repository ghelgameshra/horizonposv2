<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StrukController extends Controller
{
    public function getPesanStruk(): JsonResponse
    {
        $pesan = DB::table('setting_pesan_struk')->get();

        return response()->json([
            'data'  => $pesan,
            'pesan' => 'berhasil ambil data pesan struk'
        ]);
    }
}
