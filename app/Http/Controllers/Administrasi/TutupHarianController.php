<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Insert\TutupHarianRequest;
use App\Models\Administrasi\TutupHarian;
use App\Models\Administrasi\TutupHarianDetail;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TutupHarianController extends Controller
{
    public function data(): JsonResponse
    {
        $dataHarian = TutupHarian::with('tutupHarianDetail')
        ->select(['id', 'invno', 'tanggal_harian', 'rptotal', 'user'])
        ->get()
        ->map(function ($item) {
            return [
                'invno'          => $item->invno,
                'tanggal_harian' => $item->tanggal_harian,
                'rptotal'        => $item->rptotal,
                'user'           => $item->user,
                'selisih_fisik'  => $item->tutupHarianDetail->selisih_fisik ?? 0,
            ];
        });


        return response()->json([
            'message'   => 'Success ambil data harian',
            'data'      => [
                'harian'    => $dataHarian
            ]
        ], 201);
    }

    public function checkHarian(): JsonResponse
    {
        // Ambil tanggal transaksi yang belum masuk ke tutup_harian
        $data = DB::table(DB::raw("(SELECT tanggal_transaksi, ROW_NUMBER() OVER (PARTITION BY tanggal_transaksi ORDER BY id DESC) AS rn FROM transaksi WHERE tanggal_transaksi <= CURDATE()) as ranked_transaksi"))
            ->leftJoin('tutup_harian', 'ranked_transaksi.tanggal_transaksi', '=', 'tutup_harian.tanggal_harian')
            ->where('ranked_transaksi.rn', 1)
            ->whereNull('tutup_harian.id')
            ->select('ranked_transaksi.tanggal_transaksi as tanggal_harian')
            ->orderBy('tanggal_harian', 'desc')
            ->get();

        if ($data->isEmpty()) {
            throw new HttpResponseException(response([
                'message' => 'Proses tutup harian sudah selesai'
            ], 422));
        }

        return response()->json([
            'message'   => 'Belum tutup harian',
            'data'      => [
                'tanggal_harian' => $data->pluck('tanggal_harian')
            ]
        ], 200);
    }


    public function store(TutupHarianRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                throw new HttpResponseException(response([
                    'message' => "Password tidak sesuai"
                ], 422));
            }

            // Pastikan semua pengeluaran sudah ada image
            $pengeluaranCount = DB::table('pengeluaran')
                ->whereDate('tanggal_pengeluaran', now()->toDateString())
                ->whereNull('image')
                ->count();

            if ($pengeluaranCount > 0) {
                throw new HttpResponseException(response([
                    'message' => 'Ada pengeluaran hari yang belum input image referensi'
                ], 422));
            }

            $data = $request->validated();
            $nominals = [
                "rp100000" => 100000,
                "rp75000"  => 75000,
                "rp50000"  => 50000,
                "rp20000"  => 20000,
                "rp10000"  => 10000,
                "rp5000"   => 5000,
                "rp2000"   => 2000,
                "rp1000"   => 1000,
                "rp500"    => 500,
                "rp200"    => 200,
                "rp100"    => 100
            ];

            $total = array_sum(array_map(
                fn($key, $value) => isset($nominals[$key]) && is_numeric($value) ? $value * $nominals[$key] : 0,
                array_keys($data),
                $data
            ));

            $tanggal = Carbon::parse($data['tanggal_harian'])->format('Y-m-d');
            $data['rptotal'] = $total;
            $data['tanggal_harian'] = $tanggal;
            $data['invno'] = 'HR' . Carbon::parse($tanggal)->format('ymd/') . now()->format('mdHi');
            $data['user'] = $user->name;

            $dataHarian = TutupHarian::create($data);

            // Ambil semua transaksi valid
            $transaksi = DB::table('transaksi')
                ->whereDate('tanggal_transaksi', $tanggal)
                ->whereNotNull('invno')
                ->where('status_order', '!=', 'CANCEL SALES')
                ->get();

            $jumlah_sales         = $transaksi->count();
            $pesanan_selesai      = $transaksi->where('status_order', 'SELESAI')->count();
            $total_nominal_sales  = $transaksi->sum('total');
            $jumlah_sales_diskon  = $transaksi->filter(fn($t) => $t->diskon > 0)->count();
            $total_nominal_diskon = $transaksi->sum('diskon');

            // Pendapatan cash
            $jumlah_bayar_dpcsh = $transaksi->where('tipe_bayar', 'DPCSH')->count();
            $total_bayar_dpcsh = $transaksi->where('tipe_bayar', 'DPCSH')->sum('uang_muka');

            $jumlah_bayar_csh = $transaksi->where('tipe_bayar_pelunasan', 'CSH')->count();
            $total_bayar_csh = $transaksi->filter(fn($t) => $t->tipe_bayar_pelunasan === 'CSH')
                ->sum(fn($t) => $t->terima - $t->kembali - $t->uang_muka);

            // Pendapatan transfer
            $jumlah_bayar_dptrf = $transaksi->where('tipe_bayar', 'DPTRF')->count();
            $total_bayar_dptrf = $transaksi->where('tipe_bayar', 'DPTRF')->sum('uang_muka');

            $jumlah_bayar_trf = $transaksi->where('tipe_bayar_pelunasan', 'TRF')->count();
            $total_bayar_trf = $transaksi->filter(fn($t) => $t->tipe_bayar_pelunasan === 'TRF')
                ->sum(fn($t) => $t->terima - $t->kembali - $t->uang_muka);

            // Piutang
            $total_piutang = $transaksi->filter(fn($t) => is_null($t->tipe_bayar_pelunasan))
                ->sum(fn($t) => $t->total - $t->uang_muka);

            // Pesanan cancel
            $pesanan_cancel = DB::table('transaksi')
                ->whereDate('tanggal_transaksi', $tanggal)
                ->where('status_order', 'CANCEL SALES')
                ->count();

            TutupHarianDetail::create([
                'id_harian'             => $dataHarian->id,
                'pesanan_cancel'        => $pesanan_cancel,
                'jumlah_sales'          => $jumlah_sales,
                'pesanan_selesai'       => $pesanan_selesai,
                'total_nominal_sales'   => $total_nominal_sales,
                'jumlah_sales_diskon'   => $jumlah_sales_diskon,
                'total_nominal_diskon'  => $total_nominal_diskon,
                'jumlah_bayar_dpcsh'    => $jumlah_bayar_dpcsh,
                'total_bayar_dpcsh'     => $total_bayar_dpcsh,
                'jumlah_bayar_csh'      => $jumlah_bayar_csh,
                'total_bayar_csh'       => $total_bayar_csh,
                'jumlah_bayar_dptrf'    => $jumlah_bayar_dptrf,
                'total_bayar_dptrf'     => $total_bayar_dptrf,
                'jumlah_bayar_trf'      => $jumlah_bayar_trf,
                'total_bayar_trf'       => $total_bayar_trf,
                'rptotal'               => $total,
                'piutang'               => $total_piutang,
                'selisih_fisik'         => ($total_bayar_csh + $total_bayar_dpcsh) - $total,
            ]);

            DB::commit();

            $this->createBackupStock();

            return response()->json([
                'message' => 'Berhasil tutup harian',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    private function createBackupStock(): void
    {
        $schema = 'backup_pos';
        $timestamp = now()->format('Y_m_d_His');
        $tableName = "backup_stok_$timestamp";

        try {
            // Pastikan schema ada
            DB::statement("CREATE SCHEMA IF NOT EXISTS `$schema`");

            // Buat tabel backup dengan data dari produk
            DB::unprepared("
                CREATE TABLE `$schema`.`$tableName` AS
                SELECT
                    plu,
                    nama_produk,
                    harga_jual AS harga,
                    stok
                FROM produk
            ");

            Log::info("Berhasil buat tabel backup $schema.$tableName");
        } catch (\Throwable $th) {
            Log::error("Gagal membuat backup stok $schema.$tableName: " . $th->getMessage());
        }
    }
}
