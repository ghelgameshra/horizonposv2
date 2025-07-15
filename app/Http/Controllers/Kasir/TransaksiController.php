<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Struk\PrintStrukController;
use App\Models\Transaksi\Transaksi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function checkPromo(int $idTransaksi, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomor_telepone' => [
                'required',
                'regex:/^(\+62|0)[0-9]{9,14}$/'
            ]
        ]);

        $promo = new PromoController($validated['nomor_telepone'], $idTransaksi);
        $potongan = $promo->checkPromo();

        $orderController = new OrderController();
        $order = $orderController->order();

        return response()->json([
            'messages' => 'success check promo',
            'data'     => compact('potongan', 'order'),
        ]);
    }

    public function checkout(int $id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nomor_telepone' => ['required', 'regex:/^(\+62|0)[0-9]{9,14}$/'],
            'nama_customer'  => 'required|string|min:5|max:100',
            'terima'         => 'required|numeric|min:1',
            'tipe_bayar'     => 'required|string|min:1|max:10',
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $tipeBayar = $validated['tipe_bayar'];
        $terima = (int) $validated['terima'];
        $total = (int) $transaksi->total;

        // Validasi jika CASH/TRF tapi uang kurang
        if (in_array($tipeBayar, ['CSH', 'TRF']) && $terima < $total) {
            return response()->json([
                'message' => 'Uang diterima tidak boleh lebih kecil dari total pembayaran'
            ], 422);
        }

        // Data umum
        $commonData = [
            'invno'           => "INV" . now()->format('ymd') . str_pad($transaksi->id, 8, '0', STR_PAD_LEFT),
            'nomor_telepone'  => $validated['nomor_telepone'],
            'nama_customer'   => strtoupper($validated['nama_customer']),
            'tipe_bayar'      => $tipeBayar,
            'status_order'    => 'DALAM ANTRIAN',
            'addid'           => env('DB_USERNAME') . "@" . $request->ip() . ':' . Auth::user()->email,
        ];

        // Tambahan field tergantung tipe bayar
        $tipeDP = ['DPCSH', 'DPTRF'];
        if (in_array($tipeBayar, $tipeDP)) {
            $commonData['uang_muka'] = $terima;
        } else {
            $commonData['terima'] = $terima;
            $commonData['kembali'] = $terima - $total;
            $commonData['tipe_bayar_pelunasan'] = $tipeBayar;
        }

        $transaksi->update($commonData);

        // Jika ada kode promo
        if ($transaksi->kode_promo) {
            DB::update("UPDATE promosi SET total_penggunaan = total_penggunaan + 1 WHERE kode_promo = ?", [
                $transaksi->kode_promo
            ]);
        }



        // Cetak struk jika aktif
        $setting = DB::table('setting_struk')->where('key', 'AUPR')->first();
        if ($setting && $setting->status) {
            (new PrintStrukController($transaksi->invno))->print();
        }

        return response()->json([
            'message' => 'Pesanan baru selesai dibuat',
        ], 200);
    }
}
