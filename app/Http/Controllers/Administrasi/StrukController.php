<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Struk\PrintStrukController;
use App\Http\Requests\Insert\PrinterInsertRequest;
use App\Models\Administrasi\PrinterSetting;
use App\Models\Administrasi\StrukSetting;
use App\Models\Transaksi\Transaksi;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

require __DIR__ . '../../../../../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

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

    public function getPrinterSetting(): JsonResponse
    {
        $setting = DB::table('setting_struk')->get();

        return response()->json([
            'data'  => $setting
        ]);
    }

    public function changeStatus(Request $request): JsonResponse
    {
        $request->validate([
            'key' => ['required', 'string']
        ]);

        if(in_array($request->key, ['ISIS', 'isis'])){
            throw new HttpResponseException(response([
                'message' => 'isi struk tidak bisa dinonaktifkan'
            ], 403));
        }

        $setting = StrukSetting::where('key', $request->key)->first();
        $setting->status = $setting->status ? false : true;
        $setting->save();

        return response()->json([
            'pesan'  => "berhasil update status $request->key"
        ]);
    }

    public function reprint($invno): JsonResponse
    {
        $initPrint = new PrintStrukController($invno);
        $initPrint->print();

        return response()->json([
            'pesan'     => "berhasil reprint struk invno $invno",
        ]);
    }
}
