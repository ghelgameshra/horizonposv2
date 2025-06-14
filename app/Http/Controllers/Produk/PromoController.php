<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use App\Http\Requests\Insert\PromoRequest;
use App\Models\Produk\Promo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class PromoController extends Controller
{
    public function get()
    {
        $promosi = Promo::all()->select(['kode_promo', 'nama_promo', 'promo_member', 'tanggal_mulai', 'tanggal_selesai', 'status_promo', 'total_penggunaan']);
        return response()->json([
            'pesan'     => 'berhasil ambil data promo',
            'data'      => compact('promosi')
        ], 200);
    }

    public function detail(String $kodePromo)
    {
        $promosi = Promo::where('kode_promo', $kodePromo)->first();
        return response()->json([
            'pesan'     => 'berhasil ambil detail data promo',
            'data'      => compact('promosi')
        ], 200);
    }

    public function create(PromoRequest $request): JsonResponse
    {
        $promosi = $request->validated();
        $promosi['kode_promo'] = $this->generateKodePromo();

        try {
            Promo::create($promosi);
        } catch (\Throwable $th) {
            return response()->json([
                'message'   => 'Gagal tambah data promosi',
                'error'     => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'message'   => 'Berhasil tambah promosi',
            $promosi
        ], 201);
    }

    private function generateKodePromo(): string
    {
        do {
            $kode = strtoupper(Str::random(10));
        } while (Promo::where('kode_promo', $kode)->exists());

        return $kode;
    }

    public function destroy(String $kodePromo): JsonResponse
    {
        $data = Promo::where('kode_promo', $kodePromo)->first();
        $data->delete();

        return response()->json([
            'message' => "promo $kodePromo berhasil dihapus"
        ], 200);
    }

    public function setStatus(String $kodePromo): JsonResponse
    {
        $data = Promo::where('kode_promo', $kodePromo)->first();
        $data->status_promo = !$data->status_promo;
        $data->save();

        return response()->json([
            'message' => "promo $kodePromo berhasil diubah status"
        ], 200);
    }

    public function update(PromoRequest $request, $kodePromo): JsonResponse
    {
        $dataUpdate = $request->validated();
        $promo = Promo::where('kode_promo', $kodePromo)->first();
        $promo->update($dataUpdate);

        return response()->json([
            'message'   => "Success edit promo $kodePromo",
            $kodePromo
        ]);
    }
}
