<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Struk\PrintStrukController;
use App\Http\Requests\Update\TransaksiSelesaiRequest;
use App\Models\Produk\Produk;
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

    public function transaksiBaru($user): JsonResponse
    {
        $transaksi = Transaksi::firstOrCreate(
            ['terima' => 0, 'kasir_id' => $user, 'tipe_bayar' => null],
            ['tanggal_transaksi' => now()]
        );

        if (!Carbon::parse($transaksi->tanggal_transaksi)->isToday()) {
            $transaksi->update(['tanggal_transaksi' => now()]);
        }

        $logs = TransaksiLog::where('id_transaksi', $transaksi->id)->get();
        if ($logs->isNotEmpty()) {
            $this->updateTransaksi($logs, $transaksi);
        }

        $satuan = DB::table('ref_satuan')->select('nama_satuan', 'input_namafile', 'input_ukuran')->get();

        return response()->json([
            'pesan' => 'berhasil ambil data transaksi',
            'data' => $transaksi,
            'satuan' => $satuan
        ]);
    }

    private function updateTransaksi($logs, $transaksi): void
    {
        $subtotal = 0;
        foreach ($logs as $log) {
            $harga = $log->harga_ukuran > 0 ? $log->harga_ukuran : $log->harga_jual;
            $total = $harga * $log->jumlah;
            $subtotal += $total;

            $log->update(['total' => $total]);
        }

        $transaksi->update(['subtotal' => $subtotal]);
    }


    public function transaksiBaruDetail(Request $request): JsonResponse
    {
        $data = DB::table('transaksi_log')->where('id_transaksi', $request->id_transaksi)
        ->select(['plu', 'nama_produk', 'harga_jual', 'jumlah', 'total', 'ukuran', 'satuan', 'namafile', 'id', 'gross'])->get();

        return response()->json([
            'pesan' => 'berhasil ambil data detail transaksi',
            'data'  => $data
        ], 200);
    }

    public function getProdukJual(): JsonResponse
    {
        $data = Produk::with('kategori:id,nama_kategori')->where('bisa_jual', true)
        ->select(['plu', 'nama_produk', 'id_kategori', 'harga_jual'])->get();
        return response()->json([
            'pesan' => 'berhasil ambil data produk jual',
            'data'  => $data
        ], 200);
    }

    public function transaksiLog(Request $request): JsonResponse
    {
        $request->validate([
            'plu' => 'required',
            'idTransaksi' => 'required'
        ]);

        $log = TransaksiLog::where('plu', $request->plu)
            ->where('id_transaksi', $request->idTransaksi)
            ->first();

        if (!$log || in_array($log->satuan, ['LUAS', 'KELILING'])) {
            $this->transaksiLogNew($request->plu, $request->idTransaksi);
        } else {
            $log->increment('jumlah');
        }

        return response()->json([
            'pesan' => "PLU {$request->plu} berhasil ditambah",
        ], 201);
    }

    /*
        buat detail transaksi atau buat baru
    */
    private function transaksiLogNew($plu, $idTransaksi): void
    {
        $produk = Produk::with(['kategori'])->where('plu', $plu)->first();
        if($produk->stok <= 0 && !$produk->jual_minus){
            throw new HttpResponseException(response([
                'message' => "Stok tidak mencukupi. PLU $produk->plu tidak bisa jual minus"
            ], 404));
        }

        $statusOrder = 'DALAM ANTRIAN';
        if($produk->kategori->nama_kategori === 'JASA') $statusOrder = "SELESAI";
        TransaksiLog::create([
            'id_transaksi'      => $idTransaksi,
            'plu'               => $produk->plu,
            'nama_produk'       => $produk->nama_produk,
            'id_kategori'       => $produk->id_kategori,
            'harga_jual'        => $produk->harga_jual,
            'jumlah'            => 1,
            'satuan'            => $produk->satuan,
            'status_order'      => $statusOrder,
            'informasi_stok'    => "stok_awal=$produk->stok|stok_akhir=" . $produk->stok - 1
        ]);

        $produk->update([
            'stok' => $produk->stok - 1
        ]);
    }

    public function addfilesize(int $id_transaksi_log, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fileName'  => 'required|string|max:50',
            'size'      => 'nullable|string|max:50'
        ]);

        $transaksiLog = TransaksiLog::findOrFail($id_transaksi_log);

        $ukuran = isset($validated['size']) && stripos($validated['size'], 'X') !== false
            ? strtoupper($validated['size'])
            : null;

        $transaksiLog->update([
            'namafile'  => strtoupper($validated['fileName']),
            'ukuran'    => $ukuran
        ]);

        $this->hitungSubtotalTransaksiLog($transaksiLog);

        return response()->json([
            'message' => 'Berhasil set namafile',
            'data'    => $transaksiLog,
            'input'   => $validated
        ]);
    }

    private function hitungSubtotalTransaksiLog(TransaksiLog $log): void
    {
        $satuan = strtoupper($log->satuan);
        $hargaUkuran = $total = $log->harga_jual;

        if (in_array($satuan, ['LUAS', 'KELILING'], true) && $log->ukuran) {
            $ukuran = explode('X', strtoupper($log->ukuran));
            if (count($ukuran) === 2 && is_numeric($ukuran[0]) && is_numeric($ukuran[1])) {
                $panjang = $ukuran[0] / 100;
                $lebar   = $ukuran[1] / 100;

                $hargaUkuran = match($satuan) {
                    'LUAS'      => $panjang * $lebar * $log->harga_jual,
                    'KELILING'  => ($panjang + $lebar) * 2 * $log->harga_jual,
                };

                $total = $hargaUkuran * $log->jumlah;
            }
        } else {
            $total = $hargaUkuran * $log->jumlah;
        }

        $log->update([
            'harga_ukuran' => $hargaUkuran,
            'total'        => $total
        ]);
    }


    public function tambahQty(int $id, string $plu, int $qty): JsonResponse
    {
        $transaksiLog = TransaksiLog::findOrFail($id);
        $produk = Produk::where('plu', $plu)->firstOrFail();

        $stokTersedia = $produk->stock;
        $jumlahSebelumnya = $transaksiLog->jumlah;
        $selisihQty = $qty - $jumlahSebelumnya;

        if (!$produk->jual_minus && $selisihQty > $stokTersedia) {
            throw new HttpResponseException(response()->json([
                'message' => "Stok produk dengan PLU '{$produk->plu}' tidak mencukupi. Tersedia: {$stokTersedia}, Tambahan: {$selisihQty}"
            ], 422));
        }

        // Update stok produk
        $produk->decrement('stok', $selisihQty);

        // Update transaksi log
        $transaksiLog->update([
            'jumlah' => $qty,
            'informasi_stok' => "stok_awal=" . ($stokTersedia + $selisihQty) . "|stok_akhir=" . $produk->stok
        ]);

        $this->hitungSubtotalTransaksiLog($transaksiLog);

        return response()->json([
            'message' => "Berhasil tambah QTY $qty untuk PLU $plu",
            'data' => compact('plu', 'qty', 'transaksiLog')
        ]);
    }


    public function cekPromo(Request $request)
    {
        $promo = new getPromoController($request);
        $potongan = $promo->checkPromo();
        return $potongan;

        return response()->json([
            'pesan' => "Transaksi mendapatkan promo Rp. " . number_format($potonganMember, 0, ',', '.'),
            'data' => [
                'potongan' => $potonganMember
            ],
        ], 200);
    }


    /*
        hapus transaksi log
    */
    public function transaksiLogDelete(int $id): JsonResponse
    {
        $transaksiLog = TransaksiLog::where('id', $id)->first();

        /* update stok produk */
        Produk::where('plu', $transaksiLog->plu)->update([
            'stok'  => DB::raw('stok + ' . $transaksiLog->jumlah)
        ]);

        $transaksiLog->delete();

        return response()->json([
            'pesan' => "PLU $transaksiLog->plu dihapus",
        ], 200);
    }

    public function transaksiSelesai(TransaksiSelesaiRequest $request): JsonResponse
    {
        $dataTransaksi = $request->validated();

        $transaksi = Transaksi::where('id', $dataTransaksi['id_transaksi'])->first();
        if ($request->terima < $transaksi->total && in_array($dataTransaksi['tipe_bayar'], ['CSH', 'TRF'])) {
            throw new HttpResponseException(response([
                'message' => 'uang diterima tidak boleh lebih kecil dari total pembayaran'
            ], 422));
        }

        if(in_array($dataTransaksi['tipe_bayar'], ['DPCSH', 'DPTRF'])){
            $transaksi->update([
                'invno'         => "INV" . now()->format('ymd') . str_pad($transaksi->id, 8, '0', STR_PAD_LEFT),
                'nomor_telepone'=> $dataTransaksi['nomor_telepone'],
                'nama_customer' => strtoupper($request->nama_customer),
                'uang_muka'     => $request->terima,
                'tipe_bayar'    => $request->tipe_bayar,
                'status_order'  => 'DALAM ANTRIAN',
                'addid'         => env('DB_USERNAME') . "@" . $request->ip() . ':' . Auth::user()->email,
            ]);
        } else {
            $transaksi->update([
                'invno'         => "INV" . now()->format('ymd') . str_pad($transaksi->id, 8, '0', STR_PAD_LEFT),
                'nomor_telepone'=> $dataTransaksi['nomor_telepone'],
                'nama_customer' => strtoupper($request->nama_customer),
                'terima'        => $request->terima,
                'kembali'       => $request->terima - $transaksi->total,
                'tipe_bayar'    => $request->tipe_bayar,
                'status_order'  => 'DALAM ANTRIAN',
                'addid'         => env('DB_USERNAME') . "@" . $request->ip() . ':' . Auth::user()->email,
                'tipe_bayar_pelunasan'    => $request->tipe_bayar,
            ]);
        }

        if($transaksi->kode_promo){
            $promo = DB::table('promo')->where('kode_promo', $transaksi->kode_promo)->first();

            $member = DB::table('member')->where('id', $transaksi->id_member)->first();
            $transaksi->update([
                'nama_customer' => $member->nama_lengkap
            ]);

            $promo->update([
                'total_penggunaan'  => $promo->total_penggunaan + 1
            ]);
        }

        $cetakStruk = DB::table('setting_struk')->where('key', 'AUPR')->first();
        if($cetakStruk->status){
            $initPrint = new PrintStrukController($transaksi->invno);
            $initPrint->print();
        }

        return response()->json([
            'pesan' => "Pesanan baru selesai dibuat dan dalam pengerjaan",
        ], 200);
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
        if(!Hash::check($request->password, $user->password)){
            throw new HttpResponseException(response([
                'message' => "Password tidak sesuai"
            ], 422));
        }

        $transaksi = Transaksi::where('invno', $invno)->first();
        if($transaksi->status_order === 'CANCEL SALES'){
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

        $transaksi->update([
            'status_order'  => 'CANCEL SALES',
        ]);

        return response()->json([
            'pesan' => "Pesanan dengan no $invno berhasil di cancel",
        ]);
    }
}
