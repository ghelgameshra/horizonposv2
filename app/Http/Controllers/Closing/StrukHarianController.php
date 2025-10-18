<?php

namespace App\Http\Controllers\Closing;

use App\Http\Controllers\Administrasi\TutupHarianController;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StrukHarianController extends Controller
{
    public function reprint($tanggalHarian = null): JsonResponse
    {
        $struk = new PrintStrukHarianController($tanggalHarian);
        $struk->print();

        return response()->json([
            'message'   => "success reprint harian $tanggalHarian"
        ]);
    }

    public function sendMessage($tanggalHarian = null): JsonResponse
    {
        $msg = new TutupHarianController();
        $status = $msg->sendToBot($tanggalHarian);

        return response()->json([
            'message'   => "$status"
        ]);
    }
}
