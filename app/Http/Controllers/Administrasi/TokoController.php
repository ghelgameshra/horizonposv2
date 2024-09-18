<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Update\UpdateDataTokoRequest;
use App\Models\Administrasi\Toko;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TokoController extends Controller
{
    public function get(): JsonResponse
    {
        $toko = DB::table('toko')->first();
        return response()->json([
            'data'  => $toko,
            'pesan' => 'berhasil ambil data toko'
        ]);
    }

    public function update(UpdateDataTokoRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['nama_perusahaan'] = strtoupper($data['nama_perusahaan']);
        $data['alamat_lengkap'] = ucwords($data['alamat_lengkap']);

        Toko::first()->update($data);

        return response()->json([
            'pesan' => 'berhasil update data toko'
        ]);
    }

    public function changeLogo(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'mimes:png', 'max:2048'],
        ]);

        if($request->hasFile('file') && $request->jenis === 'logo'){
            $extension = $request->file('file')->extension();

            $fileName = "logo_" . now()->format('Y_m_d_h_i_s') . "." . $extension;
            $path = $request->file('file')->storeAs('config', $fileName);

            Toko::first()->update([
                'logo'  => $path
            ]);
        }

        if($request->hasFile('file') && $request->jenis === 'qr'){
            $extension = $request->file('file')->extension();

            $fileName = "qr_wa_" . now()->format('Y_m_d_h_i_s') . "." . $extension;
            $path = $request->file('file')->storeAs('config', $fileName);

            Toko::first()->update([
                'qr_wa'  => $path
            ]);
        }

        return response()->json([
            'pesan' =>"logo berhasil diubah"
        ]);
    }
}
