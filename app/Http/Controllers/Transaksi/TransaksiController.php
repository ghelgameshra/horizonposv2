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
            'data'  => $data
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

        return response()->json([
            'pesan' => 'berhasil ambil data transaksi',
            'data'  => $dataTransaksi
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
        ->select(['plu', 'nama_produk', 'harga_jual', 'jumlah', 'total', 'ukuran', 'satuan', 'namafile'])->get();

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
            $transaksiLog->update([
                'jumlah' => $transaksiLog->jumlah + 1
            ]);
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
        $produk = Produk::where('plu', $plu)->first();
        if($produk->stok <= 0 && !$produk->jual_minus){
            throw new HttpResponseException(response([
                'message' => "Stok tidak mencukupi. PLU $produk->plu tidak bisa jual minus"
            ], 404));
        }

        TransaksiLog::create([
            'id_transaksi'      => $idTransaksi,
            'plu'               => $produk->plu,
            'nama_produk'       => $produk->nama_produk,
            'id_kategori'       => $produk->id_kategori,
            'harga_jual'        => $produk->harga_jual,
            'jumlah'            => 1,
            'satuan'            => $produk->satuan,
            'status_order'      => "DALAM ANTRIAN",
            'informasi_stok'    => "stok_awal=$produk->stok|stok_akhir=" . $produk->stok - 1
        ]);

        $produk->update([
            'stok' => $produk->stok - 1
        ]);
    }

    /*
        tambah QTY
    */
    public function tambahQty(Request $request): JsonResponse
    {
        $request->validate([
            'id_transaksi'  => ['required', 'numeric'],
            'plu'           => ['required', 'string'],
            'qtyTambah'     => ['required', 'numeric', 'max:99999'],
            'nama_file'     => ['nullable', 'string', 'max:50'],
            'ukuran'        => ['nullable', 'string', 'max:50']
        ]);

        $transaksiLog = TransaksiLog::where('plu', $request->plu)->where('id_transaksi', $request->id_transaksi)->first();
        $produk = Produk::where('plu', $request->plu)->first();
        if($transaksiLog->satuan === 'UNIT'){

            $qtyTambahAkhir = $request->qtyTambah - $transaksiLog->jumlah;
            $qtyAkhirProduk = $produk->stok - $qtyTambahAkhir;

            $transaksiLog->update([
                'jumlah'        => DB::raw('jumlah + ' . $qtyTambahAkhir), // Tambah 1 pada jumlah
                'informasi_stok'=> "stok_awal=" . $produk->stok + 1 . "|stok_akhir=$qtyAkhirProduk"
            ]);

            $produk->update([
                'stok' => $produk->stok - $qtyTambahAkhir
            ]);
        } else {
            $this->updateQty($transaksiLog, $produk, $request->qtyTambah, $request->nama_file, $request->ukuran);
        }

        return response()->json([
            'pesan' => "berhasil tambah Qty $request->qtyTambah PLU $request->plu",
        ], 201);
    }

    /*
        tambah QTY produk dengan satuan luas atau keliling
     */
    private function updateQty($transaksiLog, $produk, $qtyTambah, $namaFile, $ukuran): void
    {
        // /* nama_file dan ukuran tidak boleh kosong salah satu */
        /* update nama file dan ukuran */
        $ukuran = str_replace(['x', 'X'], 'X', $ukuran);
        $ukuran = preg_replace('/X+/', 'X', $ukuran);
        $ukuranBaru = explode('X', $ukuran);
        $hargaMeter = 0;

        if($produk->satuan === 'LUAS'){
            /* harga_jual x (110cm/100) x (100cm/100) => 110cm/100 : ubah CM ke Meter P*L=LUAS */
            $hargaMeter = $produk->harga_jual * ($ukuranBaru[0]/100 * $ukuranBaru[1]/100);
        }

        if($produk->satuan === 'KELILING'){
            /* harga_jual x ( (110cm/100) + (100cm/100) x 2 ) => 110cm/100 : ubah CM ke Meter P+L*2=KELILING */
            $hargaMeter = $produk->harga_jual * (($ukuranBaru[0]/100 + $ukuranBaru[1]/100) * 2);
        }

        $qtyTambahAkhir = $qtyTambah - $transaksiLog->jumlah;
        $qtyAkhirProduk = $produk->stok - $qtyTambahAkhir;

        $transaksiLog->update([
            'harga_ukuran'  => $hargaMeter,
            'ukuran'        => $ukuran,
            'namafile'      => strtoupper($namaFile),
            'jumlah'        => DB::raw('jumlah + ' . $qtyTambahAkhir), // Tambah 1 pada jumlah
            'informasi_stok'=> "stok_awal=" . $produk->stok + 1 . "|stok_akhir=$qtyAkhirProduk"
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
    public function transaksiLogDelete(Request $request): JsonResponse
    {
        $transaksiLog = TransaksiLog::where('plu', $request->plu)->where('id_transaksi', $request->idTransaksi)->first();

        /* update stok produk */
        Produk::where('plu', $request->plu)->update([
            'stok'  => DB::raw('stok + ' . $transaksiLog->jumlah)
        ]);

        $transaksiLog->delete();

        return response()->json([
            'pesan' => "PLU $request->plu dihapus",
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
        $data = Transaksi::where('invno', $invno)->first();
        $statusOrder = $data->status_order;

        if ($statusOrder === "PESANAN DIAMBIL"){
            throw new HttpResponseException(response([
                'message' => "Pesanan dengan no $invno sudah diambil pada $data->updated_at"
            ], 422));
        }

        if ($data->terima === 0 || $data->status_order === "CANCEL SALES"){
            throw new HttpResponseException(response([
                'message' => "Pesanan dengan no $invno belum selesai pembayaran/ pesanan cancel"
            ], 422));
        }

        if ($statusOrder !== "SELESAI"){
            throw new HttpResponseException(response([
                'message' => "Pesanan dengan no $invno belum selesai, masih " . strtolower($statusOrder)
            ], 422));
        }

        $data->status_order = "PESANAN DIAMBIL";
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

        $transaksi->update([
            'status_order'  => 'CANCEL SALES',
        ]);

        return response()->json([
            'pesan' => "Pesanan dengan no $invno berhasil di cancel",
        ]);
    }
}
