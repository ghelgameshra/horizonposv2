<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Struk\PrintStrukController;
use App\Http\Requests\Update\TransaksiSelesaiRequest;
use App\Models\Produk\Produk;
use App\Models\Produk\Promo;
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
        $data = Transaksi::with('kasir')->where('tipe_bayar', '!=', null)->orderBy('created_at', 'desc')->get();
        return response()->json([
            'data'      => $data
        ]);
    }

    public function transaksiBaru($user): JsonResponse
    {
        // Mencari transaksi yang belum dibayar dan memiliki total 0 untuk kasir tertentu
        $dataTransaksi = Transaksi::firstOrCreate(
            ['terima' => 0, 'kasir_id' => $user, 'tipe_bayar' => null],
            ['tanggal_transaksi' => now()]
        );

        // Perbarui tanggal transaksi jika tidak sesuai dengan hari ini
        if (Carbon::parse($dataTransaksi->tanggal_transaksi)->format('Y-m-d') !== now()->format('Y-m-d')) {
            $dataTransaksi->update(['tanggal_transaksi' => now()]);
        }

        $transaksiLog = TransaksiLog::where('id_transaksi', $dataTransaksi->id)->get();
        if($transaksiLog){
            $this->updateTransaksi($transaksiLog, $dataTransaksi);
        }

        $satuan = DB::table('ref_satuan')->select(["nama_satuan","input_namafile","input_ukuran"])->get();

        return response()->json([
            'pesan'     => 'berhasil ambil data transaksi',
            'data'      => $dataTransaksi,
            'satuan'    => $satuan
        ], 200);
    }

    private function updateTransaksi($transaksiLog, $transaksi): void
    {
        $subtotal = 0;
        foreach ($transaksiLog as $value) {
            $subtotalLog = 0;
            if($value->harga_ukuran > 0){
                $subtotal += $value->harga_ukuran * $value->jumlah;
                $subtotalLog = $value->harga_ukuran * $value->jumlah;
            } else {
                $subtotal += $value->harga_jual * $value->jumlah;
                $subtotalLog = $value->harga_jual * $value->jumlah;
            }

            $value->update([
                'total' => $subtotalLog
            ]);
        }

        $transaksi->update([
            'subtotal'  => $subtotal,
        ]);
    }

    public function transaksiBaruDetail(Request $request): JsonResponse
    {
        $data = DB::table('transaksi_log')->where('id_transaksi', $request->id_transaksi)
        ->select(['plu', 'nama_produk', 'harga_jual', 'jumlah', 'total', 'ukuran', 'satuan', 'namafile', 'id'])->get();

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
        if(!$request->plu || !$request->idTransaksi){
            throw new HttpResponseException(response([
                'message' => 'silahkan pilih PLU'
            ], 404));
        }

        $transaksiLog = TransaksiLog::where('plu', $request->plu)->where('id_transaksi', $request->idTransaksi)->first();
        if(!$transaksiLog){
            $this->transaksiLogNew($request->plu, $request->idTransaksi);
        } else {
            if(in_array($transaksiLog->satuan, ['LUAS', 'KELILING'])) {
                $this->transaksiLogNew($request->plu, $request->idTransaksi);
            } else {
                $transaksiLog->update([
                    'jumlah' => $transaksiLog->jumlah + 1
                ]);
            }
        }

        return response()->json([
            'pesan' => "PLU $request->plu berhasil ditambah",
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


    public function cekPromo(Request $request): JsonResponse
    {
        $potonganHarga = 0;
        $transaksi = Transaksi::where('id', $request->id_transaksi)->first();

        /* cek diskon member */
        if( strlen($request->nomor_telepone) >= 12 && strlen($request->nomor_telepone) <= 15 ){
            $potonganHarga += $this->getDiskonMember($request->nomor_telepone, $transaksi);
        }

        $transaksi->update([
            'total'     => $transaksi->subtotal - $potonganHarga,
            'diskon'    => $potonganHarga
        ]);

        return response()->json([
            'pesan'         => "Transaksi mendapatkan promo Rp. " . number_format($potonganHarga, 0, ',', '.'),
            'data'          => [
                'potonganHarga' => $transaksi->diskon,
                'total'         => $transaksi->total,
                'subtotal'      => $transaksi->subtotal
            ],
        ], 201);
    }

    private function getDiskonMember($noTelp, $transaksi): Int
    {
        $potonganHarga = 0;
        $potonganPersen = 0;

        $isMember = DB::table('member')->where('telepone', $noTelp)->first();
        $promoMember = DB::table('promo')->where('promo_aktif', true)->where('promo_member', true)->first();

        if($isMember && $promoMember && $transaksi->subtotal >= $promoMember->minimal_belanja && $transaksi->subtotal <= $promoMember->maksimal_belanja){
            $potonganPersen = $promoMember->potongan_persentase / 100;
            $potonganHarga += $transaksi->subtotal * $potonganPersen;

            $transaksi->update([
                'kode_promo'    => $promoMember->kode_promo,
                'id_member'     => $isMember->id
            ]);
        }

        return $potonganHarga;
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
        $transaksi = Transaksi::where('id', $request->id_transaksi)->first();
        if ($request->terima < $transaksi->total && in_array($request->tipe_bayar, ['CSH', 'TRF'])) {
            throw new HttpResponseException(response([
                'message' => 'uang diterima tidak boleh lebih kecil dari total pembayaran'
            ], 422));
        }

        if(in_array($request->tipe_bayar, ['DPCSH', 'DPTRF'])){
            $transaksi->update([
                'invno'         => "INV" . now()->format('ymd') . str_pad($transaksi->id, 8, '0', STR_PAD_LEFT),
                'nomor_telepone'=> $request->nomor_telepone,
                'nama_customer' => strtoupper($request->nama_customer),
                'uang_muka'     => $request->terima,
                'tipe_bayar'    => $request->tipe_bayar,
                'status_order'  => 'DALAM ANTRIAN',
                'addid'         => env('DB_USERNAME') . "@" . $request->ip() . ':' . Auth::user()->email,
            ]);
        } else {
            $transaksi->update([
                'invno'         => "INV" . now()->format('ymd') . str_pad($transaksi->id, 8, '0', STR_PAD_LEFT),
                'nomor_telepone'=> $request->nomor_telepone,
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
            $promo = Promo::where('kode_promo', $transaksi->kode_promo)->first();

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
