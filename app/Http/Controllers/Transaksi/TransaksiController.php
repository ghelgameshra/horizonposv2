<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Transaksi\Transaksi;
use App\Models\Transaksi\TransaksiLog;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TransaksiController extends Controller
{
    public function getTransaksi(Request $request): JsonResponse
    {
        $data = Transaksi::with('kasir')
            ->whereNotNull('tipe_bayar')
            ->latest()
            ->get();

        return response()->json(['data' => $data]);
    }

    public function show(String $invno): JsonResponse
    {
        $data = Transaksi::with(['transaksiLog', 'kasir'])->where('invno', $invno)->first();

        return response()->json([
            'pesan' => "berhasil ambil data detail $invno",
            'data'  => $data
        ]);
    }

    public function ambil(String $invno): JsonResponse
    {
        $data = Transaksi::where('invno', $invno)->firstOrFail();

        // Ambil semua log transaksi terlebih dahulu untuk meminimalkan akses ke database
        $transaksiLogs = TransaksiLog::where('id_transaksi', $data->id)->get();
        $totalOrder = $transaksiLogs->count();
        $orderSelesai = $transaksiLogs->where('status_order', 'SELESAI')->count();

        // Periksa jika semua status log telah selesai dan update jika diperlukan
        if ($totalOrder === $orderSelesai && $data->status_order !== 'SELESAI') {
            $data->status_order = 'SELESAI';
            $data->save();
        }

        // Validasi status order dan pembayaran
        if ($data->status_order === 'PESANAN DIAMBIL') {
            throw new HttpResponseException(response()->json([
                'message' => "Pesanan dengan no $invno sudah diambil pada {$data->updated_at}"
            ], 422));
        }

        if ($data->terima === 0 || $data->status_order === 'CANCEL SALES') {
            throw new HttpResponseException(response()->json([
                'message' => "Pesanan dengan no $invno belum selesai pembayaran/ pesanan cancel"
            ], 422));
        }

        if ($data->status_order !== 'SELESAI') {
            throw new HttpResponseException(response()->json([
                'message' => "Pesanan dengan no $invno belum selesai, masih " . strtolower($data->status_order)
            ], 422));
        }

        // Set status order menjadi 'PESANAN DIAMBIL'
        $data->status_order = 'PESANAN DIAMBIL';
        $data->save();

        return response()->json([
            'pesan' => "Pesanan dengan no $invno berhasil diambil",
        ]);
    }

    public function cancel(String $invno, Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string']
        ]);

        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            throw new HttpResponseException(response([
                'message' => "Password tidak sesuai"
            ], 422));
        }

        $transaksi = Transaksi::with('transaksiLog')->where('invno', $invno)->first();

        if (!$transaksi) {
            throw new HttpResponseException(response([
                'message' => "Transaksi tidak ditemukan"
            ], 404));
        }

        if ($transaksi->status_order === 'CANCEL SALES') {
            throw new HttpResponseException(response([
                'message' => "Status transaksi sudah cancel"
            ], 422));
        }

        if (!Carbon::parse($transaksi->tanggal_transaksi)->isSameDay(now())) {
            throw new HttpResponseException(response([
                'message' => "Tidak bisa cancel transaksi yang sudah lebih hari",
                'data'    => $transaksi->tanggal_transaksi . " | " . now()->toDateString()
            ], 422));
        }

        DB::transaction(function () use ($transaksi) {
            // Ambil semua transaksi log yang terkait
            $logs = $transaksi->transaksiLog; // pastikan relasi logs didefinisikan di model Transaksi

            foreach ($logs as $log) {
                $produk = $log->produk; // pastikan relasi produk didefinisikan di model TransaksiLog

                if ($produk) {
                    $produk->stok += $log->jumlah;
                    $produk->save();
                }
                $log->status_order = 'CANCEL SALES';
                $log->save();
            }

            // Update status transaksi
            $transaksi->update([
                'status_order' => 'CANCEL SALES',
            ]);
        });

        return response()->json([
            'pesan' => "Pesanan dengan no $invno berhasil cancel",
        ]);
    }

}
