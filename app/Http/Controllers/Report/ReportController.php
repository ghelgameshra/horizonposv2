<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    function hitungDataLaporan(string $tgl_awal, string $tgl_akhir): array
    {
        $data = DB::table('transaksi')
            ->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir])
            ->whereNotNull('invno')
            ->get();

        $dataValid = $data->where('status_order', '!=', 'CANCEL SALES');

        $totalPesanan           = $dataValid->count();
        $totalPesananSelesai    = $dataValid->where('status_order', 'SELESAI')->count();
        $totalPesananDiambil    = $dataValid->where('status_order', 'PESANAN DIAMBIL')->count();
        $totalPesananDibatalkan = $data->where('status_order', 'CANCEL SALES')->count();

        $pendapatanCash = $dataValid->filter(function ($item) {
            return $item->tipe_bayar === 'DPCSH' || $item->tipe_bayar_pelunasan === 'CSH';
        })->sum(function ($item) {
            $uang_muka = ($item->tipe_bayar === 'DPCSH') ? $item->uang_muka : 0;
            $pelunasan = ($item->tipe_bayar_pelunasan === 'CSH') ? ($item->terima - $item->kembali - $item->uang_muka) : 0;
            return $uang_muka + $pelunasan;
        });

        $pendapatanNonTunai = $dataValid->filter(function ($item) {
            return $item->tipe_bayar === 'DPTRF' || $item->tipe_bayar_pelunasan === 'TRF';
        })->sum(function ($item) {
            $uang_muka = ($item->tipe_bayar === 'DPTRF') ? $item->uang_muka : 0;
            $pelunasan = ($item->tipe_bayar_pelunasan === 'TRF') ? ($item->terima - $item->kembali - $item->uang_muka) : 0;
            return $uang_muka + $pelunasan;
        });

        $totalPendapatan = $pendapatanCash + $pendapatanNonTunai;

        $totalPiutang = $dataValid->filter(function ($item) {
            return is_null($item->tipe_bayar_pelunasan);
        })->sum(function ($item) {
            return max(0, $item->total - $item->uang_muka);
        });

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

        $totalPenjualanPerTgl = [];
        foreach ($dataValid as $value) {
            $keyTemp = str_replace('-', '', $value->tanggal_transaksi);
            if (!isset($totalPenjualanPerTgl[$keyTemp])) {
                $totalPenjualanPerTgl[$keyTemp] = 0;
            }
            $totalPenjualanPerTgl[$keyTemp]++;
        }

        return [
            'totalPesanan'           => $totalPesanan,
            'totalPesananSelesai'    => $totalPesananSelesai,
            'totalPesananDiambil'    => $totalPesananDiambil,
            'totalPesananDibatalkan' => $totalPesananDibatalkan,
            'totalPendapatan'        => $totalPendapatan,
            'pendapatanCash'         => $pendapatanCash,
            'pendapatanNonTunai'     => $pendapatanNonTunai,
            'piutang'                => $totalPiutang,
            'kategori'               => $kategori->map(fn($k) => ['nama_kategori' => $k->nama_kategori]),
            'jumlahPenjualan'        => $jumlahPenjualan,
            'totalProdukTerjual'     => $totalProdukTerjual,
            'totalProdukJual'        => $totalProdukJual,
            'totalPenjualanPerTgl'   => $totalPenjualanPerTgl,
        ];
    }
}
