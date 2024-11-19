<?php

namespace App\Http\Controllers\Order;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi\Transaksi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi\TransaksiLog;
use App\Models\Transaksi\WorkOrder;

class WorkOrderController extends Controller
{
    public function statusOrder(): JsonResponse
    {
        $statusOrder = DB::table('ref_status_order')->where('nama_status', '!=', 'PESANAN DIAMBIL')->select(['nama_status'])->get();

        return response()->json([
            'pesan' => 'berhasil ambil data status order',
            'data'  => [
                'statusOrder' => $statusOrder
            ]
        ]);
    }

    public function data($categoryId, $progress): JsonResponse
    {
        $order = DB::table('transaksi_log AS t')
        ->join('ref_kategori AS r', 't.id_kategori', 'r.id')->where('t.status_order', strtoupper($progress))
        ->where('r.id', $categoryId)->select(['t.id', 't.namafile', 't.nama_produk', 'r.nama_kategori AS kategori', 't.ukuran', 't.status_order', 't.jumlah', 't.worker'])->get();

        return response()->json([
            'pesan' => "berhasil ambil data $progress",
            'data'  => [
                'order' => $order,
            ]
        ]);
    }

    public function updateProgress(Int $id, String $nextProgress, Request $request): JsonResponse
    {
        $order = TransaksiLog::with(['kategori'])->findOrFail($id);

        if ($order->status_order === 'SELESAI') {
            return response()->json(['message' => 'Progress pesanan telah selesai'], 422);
        }

        $order->update([
            'status_order'  => $nextProgress,
            'worker'        => Auth::user()->name,
            'worker_addid'  => $request->ip()
        ]);

        try {
            WorkOrder::create([
                'id_transaksi'      => $order->id_transaksi,
                'id_transaksi_log'  => $order->id,
                'namafile'          => $order->namafile,
                'nama_produk'       => $order->nama_produk,
                'jumlah'            => $order->jumlah,
                'ukuran'            => $order->ukuran,
                'kategori'          => $order->kategori->nama_kategori,
                'worker'            => Auth::user()->name,
                'addid'             => $request->ip(),
                'progress'          => $nextProgress
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 422);
        }

        // Ambil semua log transaksi sekali saja
        $transaksiLogs = TransaksiLog::where('id_transaksi', $order->id_transaksi)->get();
        $totalOrder = $transaksiLogs->count();
        $orderSelesai = $transaksiLogs->where('status_order', 'SELESAI')->count();

        // Periksa jika semua status log telah selesai
        if ($totalOrder === $orderSelesai) {
            Transaksi::find($order->id_transaksi)->update(['status_order' => 'SELESAI']);
        }

        return response()->json([
            'pesan' => "Progress pesanan berhasil diupdate menjadi " . strtolower($nextProgress)
        ]);
    }
}
