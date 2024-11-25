<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Insert\TutupHarianRequest;
use App\Models\Administrasi\TutupHarian;
use App\Models\Administrasi\TutupHarianDetail;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TutupHarianController extends Controller
{
    public function data(): JsonResponse
    {
        $dataHarian = DB::table('tutup_harian')->select(['invno', 'created_at AS tanggal_harian', 'rptotal', 'user'])->get();

        return response()->json([
            'message'   => 'Success ambil data harian',
            'data'      => [
                'harian'    => $dataHarian
            ]
        ], 201);
    }

    public function checkHarian(): JsonResponse
    {
        $dataHarian = DB::table('tutup_harian')->whereDate('tanggal_harian', now()->format('Y-m-d'))->get();
        if($dataHarian->count() > 0){
            throw new HttpResponseException(response([
                'message' => 'Proses tutup harian sudah selesai'
            ], 422));
        }

        return response()->json([
            'message'   => 'Belum tutup harian',
        ], 201);
    }

    public function store(TutupHarianRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            // Validasi password user
            $user = Auth::user();
            if (!Hash::check($request->password, $user->password)) {
                throw new HttpResponseException(response([
                    'message' => "Password tidak sesuai"
                ], 422));
            }

            // Periksa pengeluaran tanpa image
            $pengeluaranCount = DB::table('pengeluaran')
                ->whereDate('tanggal_pengeluaran', now()->toDateString())
                ->whereNull('image')
                ->count();

            if ($pengeluaranCount > 0) {
                throw new HttpResponseException(response([
                    'message' => 'Ada pengeluaran hari yang belum submit image referensi'
                ], 422));
            }

            // Data validasi dan kalkulasi total
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

            $data['rptotal'] = $total;
            $data['tanggal_harian'] = now()->format('Y-m-d');
            $data['invno'] = 'HR' . now()->format('ymd/h/i');
            $data['user'] = $user->name;

            // Buat data harian
            $dataHarian = TutupHarian::create($data);

            // Ambil data sales dan ringkasan
            $salesSummary = DB::table('transaksi')
                ->selectRaw("
                    COUNT(*) AS jumlah_sales,
                        COALESCE(SUM(CASE WHEN status_order = 'SELESAI' THEN 1 ELSE 0 END), 0) AS pesanan_selesai,
                        COALESCE(SUM(total), 0) AS total_nominal_sales,
                        COALESCE(SUM(CASE WHEN diskon > 0 THEN 1 ELSE 0 END), 0) AS jumlah_sales_diskon,
                        COALESCE(SUM(CASE WHEN diskon > 0 THEN diskon ELSE 0 END), 0) AS total_nominal_diskon,
                        COALESCE(SUM(CASE WHEN tipe_bayar = 'DPCSH' THEN 1 ELSE 0 END), 0) AS jumlah_bayar_dpcsh,
                        COALESCE(SUM(CASE WHEN tipe_bayar = 'DPCSH' THEN uang_muka ELSE 0 END), 0) AS total_bayar_dpcsh,
                        COALESCE(SUM(CASE WHEN tipe_bayar = 'CSH' THEN 1 ELSE 0 END), 0) AS jumlah_bayar_csh,
                        COALESCE(SUM(CASE WHEN tipe_bayar = 'CSH' THEN total ELSE 0 END), 0) AS total_bayar_csh,
                        COALESCE(SUM(CASE WHEN tipe_bayar = 'DPTRF' THEN 1 ELSE 0 END), 0) AS jumlah_bayar_dptrf,
                        COALESCE(SUM(CASE WHEN tipe_bayar = 'DPTRF' THEN uang_muka ELSE 0 END), 0) AS total_bayar_dptrf,
                        COALESCE(SUM(CASE WHEN tipe_bayar = 'TRF' THEN 1 ELSE 0 END), 0) AS jumlah_bayar_trf,
                        COALESCE(SUM(CASE WHEN tipe_bayar = 'TRF' THEN total ELSE 0 END), 0) AS total_bayar_trf
                ")
                ->whereDate('tanggal_transaksi', $dataHarian->tanggal_harian)
                ->whereNotNull('invno')
                ->where('status_order', '!=', 'CANCEL SALES')
                ->first();

            // Hitung piutang
            $piutang = DB::table('transaksi')
                ->whereDate('tanggal_transaksi', $dataHarian->tanggal_harian)
                ->whereNull('tipe_bayar_pelunasan')
                ->sum(DB::raw('total - uang_muka'));

            // Data detail harian
            $totalSalesCancel = DB::table('transaksi')
            ->whereDate('tanggal_transaksi', $dataHarian->tanggal_harian)->where('status_order', 'CANCEL SALES')->count();
            TutupHarianDetail::create([
                'id_harian'             => $dataHarian->id,
                'pesanan_cancel'        => $totalSalesCancel,

                /*  */
                'jumlah_sales'          => $salesSummary->jumlah_sales,
                'pesanan_selesai'       => $salesSummary->pesanan_selesai,
                'total_nominal_sales'   => $salesSummary->total_nominal_sales,
                'jumlah_sales_diskon'   => $salesSummary->jumlah_sales_diskon,
                'total_nominal_diskon'  => $salesSummary->total_nominal_diskon,
                'jumlah_bayar_dpcsh'    => $salesSummary->jumlah_bayar_dpcsh,
                'total_bayar_dpcsh'     => $salesSummary->total_bayar_dpcsh,
                'jumlah_bayar_csh'      => $salesSummary->jumlah_bayar_csh,
                'total_bayar_csh'       => $salesSummary->total_bayar_csh,
                'jumlah_bayar_dptrf'    => $salesSummary->jumlah_bayar_dptrf,
                'total_bayar_dptrf'     => $salesSummary->total_bayar_dptrf,
                'jumlah_bayar_trf'      => $salesSummary->jumlah_bayar_trf,
                'total_bayar_trf'       => $salesSummary->total_bayar_trf,
                'rptotal'               => $total,
                'piutang'               => $piutang,
                'selisih_fisik'         => $salesSummary->total_bayar_csh - $total,
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

    private function createBackupStock()
    {
        DB::beginTransaction();
        try {
            DB::rollBack();
            /* buat tabel backup untuk stock */
            $tableName = "backup_stok_" . now()->format('Y_m_d');
            DB::select("CREATE TABLE $tableName SELECT plu, nama_produk, harga_jual AS harga, stok FROM produk");
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Terjadi kesalahan',
                'error'   => $th->getMessage()
            ], 500);
        }
    }
}
