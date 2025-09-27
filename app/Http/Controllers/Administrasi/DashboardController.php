<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Report\ReportController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function data(Request $request): JsonResponse
    {
         if (!$request->tgl_awal || !$request->tgl_akhir) {
            return response()->json(['message' => 'Tanggal tidak valid'], 422);
        }

        $report = new ReportController();
        $data = $report->hitungDataLaporan($request->tgl_awal, $request->tgl_akhir);

        return response()->json(['data' => $data]);
    }
}
