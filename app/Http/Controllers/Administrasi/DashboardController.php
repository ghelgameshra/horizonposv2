<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function data(Request $request): JsonResponse
    {
        if (!$request->tgl_awal && !$request->tgl_akhir) {
            throw new HttpResponseException(response([
                'message' => 'terdapat kesalahan'
            ], 422));
        }

        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;

        $data = DB::table('transaksi')
            ->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir])
            ->whereNotNull('invno')
            ->where('status_order', '!=', 'CANCEL SALES')
            ->get();

        $totalPesanan           = $data->count();
        $totalPesananSelesai    = $data->where('status_order', 'SELESAI')->count();
        $totalPesananDiambil    = $data->where('status_order', 'PESANAN DIAMBIL')->count();

        // Hitung pendapatan cash
        $pendapatanCash = $data->filter(function ($item) {
            return $item->tipe_bayar === 'DPCSH' || $item->tipe_bayar_pelunasan === 'CSH';
        })->sum(function ($item) {
            $uang_muka = $item->tipe_bayar === 'DPCSH' && $item->tipe_bayar ? $item->uang_muka : 0;
            $pelunasan = $item->tipe_bayar_pelunasan === 'CSH' ? ($item->terima - $item->kembali - $item->uang_muka) : 0;
            return $uang_muka + $pelunasan;
        });

        // Hitung pendapatan non tunai
        $pendapatanNonTunai = $data->filter(function ($item) {
            return $item->tipe_bayar === 'DPTRF' || $item->tipe_bayar_pelunasan === 'TRF';
        })->sum(function ($item) {
            $uang_muka = $item->tipe_bayar === 'DPTRF' ? $item->uang_muka : 0;
            $pelunasan = $item->tipe_bayar_pelunasan === 'TRF' ? ($item->terima - $item->kembali - $item->uang_muka) : 0;
            return $uang_muka + $pelunasan;
        });

        $totalPendapatan = $pendapatanCash + $pendapatanNonTunai;

        // Hitung piutang
        $totalPiutang = $data->filter(function ($item) {
            return is_null($item->tipe_bayar_pelunasan);
        })->sum(function ($item) {
            return max(0, $item->total - $item->uang_muka);
        });

        // Penjualan per kategori
        $kategori = DB::table('ref_kategori')->orderBy('nama_kategori')->get();

        $dataPenjualan = DB::table('transaksi_log AS t')
            ->join('ref_kategori AS r', 'r.id', '=', 't.id_kategori')
            ->whereBetween(DB::raw('DATE(t.created_at)'), [$tgl_awal, $tgl_akhir])
            ->select(['r.nama_kategori', 't.jumlah'])
            ->orderBy('r.nama_kategori')
            ->get();

        $jumlahPenjualan = [];
        $totalProdukTerjual = 0;
        foreach ($kategori as $value) {
            $jumlahPenjualan[$value->nama_kategori] = 0;
        }

        foreach ($dataPenjualan as $value) {
            $jumlahPenjualan[$value->nama_kategori] += $value->jumlah;
            $totalProdukTerjual += $value->jumlah;
        }

        // Total penjualan per produk
        $totalProdukLog = DB::table('transaksi_log')
            ->whereBetween(DB::raw('DATE(created_at)'), [$tgl_awal, $tgl_akhir])
            ->select(['nama_produk', 'jumlah'])
            ->orderBy('nama_produk')
            ->get();

        $totalProdukJual = [];
        foreach ($totalProdukLog as $value) {
            if (!isset($totalProdukJual[$value->nama_produk])) {
                $totalProdukJual[$value->nama_produk] = 0;
            }
            $totalProdukJual[$value->nama_produk] += $value->jumlah;
        }

        // Penjualan per tanggal
        $totalPenjualanPerTgl = [];
        foreach ($data as $value) {
            $keyTemp = str_replace('-', '', $value->tanggal_transaksi);
            if (!isset($totalPenjualanPerTgl[$keyTemp])) {
                $totalPenjualanPerTgl[$keyTemp] = 0;
            }
            $totalPenjualanPerTgl[$keyTemp]++;
        }

        return response()->json([
            'data' => [
                'totalPesanan'          => $totalPesanan,
                'totalPesananSelesai'   => $totalPesananSelesai,
                'totalPesananDiambil'   => $totalPesananDiambil,
                'totalPendapatan'       => $totalPendapatan,
                'pendapatanCash'        => $pendapatanCash,
                'pendapatanNonTunai'    => $pendapatanNonTunai,
                'piutang'               => $totalPiutang,
                'kategori'              => $kategori->map(fn($k) => ['nama_kategori' => $k->nama_kategori]),
                'jumlahPenjualan'       => $jumlahPenjualan,
                'totalProdukTerjual'    => $totalProdukTerjual,
                'totalProdukJual'       => $totalProdukJual,
                'totalPenjualanPerTgl'  => $totalPenjualanPerTgl,
            ]
        ]);
    }
}
