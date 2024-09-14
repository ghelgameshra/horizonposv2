<?php

use App\Http\Controllers\Administrasi\KaryawanController;
use App\Http\Controllers\Administrasi\MemberController;
use App\Http\Controllers\Produk\KategoriController;
use App\Http\Controllers\Produk\ProdukController;
use App\Http\Controllers\Produk\PromoController;
use App\Http\Controllers\Select2\Select2Controller;
use App\Http\Controllers\Transaksi\TransaksiController;
use App\Models\Transaksi\Transaksi;
use Illuminate\Support\Facades\Route;

/*
    Produk route
*/
Route::post('/produk', [ProdukController::class, 'upsert'])->name('produk.upsert');
Route::post('/produk-add', [ProdukController::class, 'insert'])->name('produk.insert');
Route::delete('/delete-produk', [ProdukController::class, 'destroy'])->name('produk.delete');
Route::put('/produk-activate-status/{plu}', [ProdukController::class, 'activateStatus'])->name('activateStatus');
Route::put('/produk-activate-status-jual-minus/{plu}', [ProdukController::class, 'activateStatusJual'])->name('activateStatusJual');

/*
    Kategori Route
*/
Route::post('kategori-add', [KategoriController::class, 'insert'])->name('kategori.insert');

/*
    Select2 Route
*/
Route::get('select2-jenis-ukuran', [Select2Controller::class, 'select2JenisUkuran'])->name('select2JenisUkuran');
Route::get('select2-kategori-produk', [Select2Controller::class, 'select2JenisKategori'])->name('select2JenisKategori');
Route::get('select2-satuan-produk', [Select2Controller::class, 'select2JenisSatuan'])->name('select2JenisSatuan');
Route::get('select2-jabatan', [Select2Controller::class, 'select2Jabatan'])->name('select2Jabatan');
Route::get('select2-agama', [Select2Controller::class, 'select2Agama'])->name('select2Agama');
Route::get('select2-pendidikan-terakhir', [Select2Controller::class, 'select2Pendidikan'])->name('select2Pendidikan');


/*
    Member Route
*/
Route::delete('member', [MemberController::class, 'destroy'])->name('member.delete');
Route::post('member', [MemberController::class, 'insert'])->name('member.insert');


/*
    Promo Route
*/
Route::delete('promo', [PromoController::class, 'destroy'])->name('promo.delete');

/*
    Karyawan Route
*/
Route::get('karyawan/{nik}', [KaryawanController::class, 'detail'])->name('karyawan.detail');
Route::post('karyawan', [KaryawanController::class, 'upsert'])->name('karyawan.upsert');
Route::post('karyawan-baru', [KaryawanController::class, 'insert'])->name('karyawan.insert');
Route::delete('karyawan', [KaryawanController::class, 'delete'])->name('karyawan.delete');

/*
    Kasir Route
*/
Route::get('kasir/transaksi-baru-detail', [TransaksiController::class, 'transaksiBaruDetail'])->name('transaksiBaruDetail');
Route::post('kasir/transaksi-baru/{user}', [TransaksiController::class, 'transaksiBaru'])->name('transaksiBaru');
Route::post('kasir/transaksi-baru-log', [TransaksiController::class, 'transaksiLog'])->name('transaksiLog');
Route::delete('kasir/transaksi-baru-log', [TransaksiController::class, 'transaksiLogDelete'])->name('transaksiLogDelete');
Route::post('kasir/transaksi-tambah-qty', [TransaksiController::class, 'tambahQty'])->name('tambahQty');
Route::post('kasir/transaksi-cek-promo', [TransaksiController::class, 'cekPromo'])->name('cekPromo');
Route::put('kasir/transaksi-baru', [TransaksiController::class, 'transaksiSelesai'])->name('transaksiSelesai');

/*
    Datatable ajx route
*/
Route::get('get-produk', [ProdukController::class, 'get'])->name('produk.get');
Route::get('get-member', [MemberController::class, 'get'])->name('member.get');
Route::get('get-promo', [PromoController::class, 'get'])->name('promo.get');
Route::get('get-karyawan', [KaryawanController::class, 'get'])->name('karyawan.get');
Route::get('get-produk-jual', [TransaksiController::class, 'getProdukJual'])->name('getProdukJual');
