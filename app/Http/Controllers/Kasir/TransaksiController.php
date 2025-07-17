<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Struk\PrintStrukController;
use App\Models\Administrasi\Member;
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

    function generateInvno(): string
    {
        $today = now()->format('ymd');
        $prefix = "INV" . $today;

        $lastInv = Transaksi::whereDate('tanggal_transaksi', now()->toDateString())->whereNotNull('invno')->latest('invno')->value('invno');

        $lastNumber = 0;
        if ($lastInv && str_starts_with($lastInv, $prefix)) {
            $lastNumber = (int) substr($lastInv, -8);
        }

        $nextNumber = str_pad($lastNumber + 1, 8, '0', STR_PAD_LEFT);
        return $prefix . $nextNumber;
    }

    public function checkout(int $id, Request $request): JsonResponse
    {
        return DB::transaction(function () use ($id, $request) {
            $validated = $request->validate([
                'nomor_telepone' => ['required', 'regex:/^(\+62|0)[0-9]{9,14}$/'],
                'nama_customer'  => 'required|string|min:2|max:100',
                'terima'         => 'required|numeric',
                'tipe_bayar'     => 'required|string|min:1|max:10',
            ]);

            $member     = Member::where('telepone', $validated['nomor_telepone'])->first();
            $transaksi  = Transaksi::findOrFail($id);
            $tipeBayar  = $validated['tipe_bayar'];
            $terima     = (int) $validated['terima'];
            $total      = (int) $transaksi->total;

            if (in_array($tipeBayar, ['CSH', 'TRF']) && $terima < $total) {
                return response()->json([
                    'message' => 'Uang diterima tidak boleh lebih kecil dari total pembayaran untuk tipe bayar cash dan transfer'
                ], 422);
            }

            // Generate invno yang urut per hari
            $invno = $this->generateInvno();

            $commonData = [
                'invno'           => $invno,
                'nomor_telepone'  => $validated['nomor_telepone'],
                'nama_customer'   => $member ? $member->nama_lengkap : strtoupper($validated['nama_customer']),
                'tipe_bayar'      => $tipeBayar,
                'status_order'    => 'DALAM ANTRIAN',
                'addid'           => env('DB_USERNAME') . "@" . $request->ip() . ':' . Auth::user()->email,
            ];

            $tipeDP = ['DPCSH', 'DPTRF'];
            if (in_array($tipeBayar, $tipeDP)) {
                $commonData['uang_muka'] = $terima;
            } else {
                $commonData['terima'] = $terima;
                $commonData['kembali'] = $terima - $total;
                $commonData['tipe_bayar_pelunasan'] = $tipeBayar;
            }

            $transaksi->update($commonData);

            if ($transaksi->kode_promo) {
                DB::update("UPDATE promosi SET total_penggunaan = total_penggunaan + 1 WHERE kode_promo = ?", [
                    $transaksi->kode_promo
                ]);
            }

            // Cetak struk jika aktif
            $setting = DB::table('setting_struk')->where('key', 'AUPR')->first();
            if ($setting && $setting->status) {
                (new PrintStrukController($invno))->print();
            }

            return response()->json([
                'message' => 'Pesanan baru selesai dibuat',
            ], 200);
        }, 3);
    }
}
