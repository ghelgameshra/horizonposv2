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
        if(!$request->tgl_awal && !$request->tgl_akhir){
            throw new HttpResponseException(response([
                'message' => 'terdapat kesalahan'
            ], 422));
        }

        $tgl_awal = $request->tgl_awal;
        $tgl_akhir = $request->tgl_akhir;
        $data = DB::table('transaksi')->whereBetween('tanggal_transaksi', [$tgl_awal, $tgl_akhir])
        ->whereNotNull('invno')->where('status_order', '!=', 'CANCEL SALES')->get();


        $totalPesanan           = $data->count();
        $totalPesananSelesai    = $data->where('status_order', 'SELESAI')->count();
        $totalPesananDiambil    = $data->where('status_order', 'PESANAN DIAMBIL')->count();
        $pendapatanCash         = $data->where('tipe_bayar', 'DPCSH')->sum('uang_muka');
        $pendapatanCash         += $data->where('tipe_bayar', 'CSH')->sum('total');
        $pendapatanNonTunai     = $data->where('tipe_bayar', 'TRF')->sum('total');
        $pendapatanNonTunai     += $data->where('tipe_bayar', 'DPTRF')->sum('uang_muka');
        $totalPendapatan        = $pendapatanCash + $pendapatanNonTunai;

        $piutang = $data->whereNull('tipe_bayar_pelunasan');
        $totalPiutang = 0;
        foreach ($piutang as $value) {
            $totalPiutang += $value->total - $value->uang_muka;
        }


        /* hitung penjualan by kategori */
        $kategori = DB::table('ref_kategori')->orderBy('nama_kategori')->get();
        $dataPenjualan = DB::table('transaksi_log AS t')
        ->join('ref_kategori AS r', 'r.id', '=', 't.id_kategori')
        ->whereBetween(DB::raw('DATE(t.created_at)'), [$tgl_awal, $tgl_akhir]) // Menggunakan whereBetween
        ->select(['r.nama_kategori', 't.jumlah'])
        ->orderBy('r.nama_kategori')
        ->get();


        $jumlahPenjualan = array();
        $totalProdukTerjual = 0;
        foreach ($kategori as $key => $value) {
            if (!isset($jumlahPenjualan[$value->nama_kategori])) {
                $jumlahPenjualan[$value->nama_kategori] = 0;
            }
        }

        foreach ($dataPenjualan as $value) {
            // Tambahkan jumlah penjualan ke kategori yang sesuai
            $jumlahPenjualan[$value->nama_kategori] += $value->jumlah;
            $totalProdukTerjual += $value->jumlah;
        }


        /* hitung total penjualan by produk */
        $totalProdukLog = DB::table('transaksi_log')->whereBetween(DB::raw('DATE(created_at)'), [$tgl_awal, $tgl_akhir])
        ->select(['nama_produk', 'jumlah'])->orderBy('nama_produk')->get();
        $totalProdukJual = array();
        foreach ($totalProdukLog as $key => $value) {
            if (!isset($totalProdukJual[$value->nama_produk])) {
                $totalProdukJual[$value->nama_produk] = 0;
            }
            $totalProdukJual[$value->nama_produk] += $value->jumlah;
        }

        /* hitung total penjualan pertanggal */
        $totalPenjualanPerTgl = array();
        foreach ($data as $key => $value) {
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
                'kategori'              => $kategori->select(['nama_kategori']),
                'jumlahPenjualan'       => $jumlahPenjualan,
                'totalProdukTerjual'    => $totalProdukTerjual,
                'totalProdukJual'       => $totalProdukJual,
                'totalPenjualanPerTgl'  => $totalPenjualanPerTgl,
            ]
        ]);
    }
}
