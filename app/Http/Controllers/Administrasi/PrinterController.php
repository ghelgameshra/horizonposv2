<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Insert\PrinterInsertRequest;
use App\Models\Administrasi\PrinterSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PrinterController extends Controller
{
    public function printer(): JsonResponse
    {
        $printer = DB::table('setting_printer')->get();

        return response()->json([
            'data'  => $printer
        ]);
    }

    public function insert(PrinterInsertRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['username_printer'] = Crypt::encryptString($data['username_printer']);
        $data['password_printer'] = Crypt::encryptString($data['password_printer']);
        PrinterSetting::create($data);

        return response()->json([
            'pesan'  => "berhasil tambah data printer"
        ], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        PrinterSetting::find($request->id)->delete();
        return response()->json([
            'pesan'  => "berhasil hapus data printer"
        ], 201);
    }
}
