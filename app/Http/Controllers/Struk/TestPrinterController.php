<?php

namespace App\Http\Controllers\Struk;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestPrinterController extends Controller
{
    public function test(Request $request): JsonResponse
    {
        $request->validate(['jenis_printer' => ['required', 'string']]);

        if($request->jenis_printer === 'ANTRIAN'){
            $label = new AntrianStrukController();
            $label->test();
        }

        if($request->jenis_printer === 'STRUK'){
            $label = new PrintStrukController();
            $label->test();
        }

        return response()->json([
            'pesan' => "berhasil test printer $request->jenis_printer"
        ]);
    }
}
