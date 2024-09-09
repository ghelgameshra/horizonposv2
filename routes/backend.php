<?php

use App\Http\Controllers\Administrasi\MemberController;
use App\Http\Controllers\Produk\KategoriController;
use App\Http\Controllers\Produk\ProdukController;
use App\Http\Controllers\Select2\Select2Controller;
use Illuminate\Support\Facades\Route;

/*
    Produk route
*/
Route::post('/produk', [ProdukController::class, 'upsert'])->name('produk.upsert');
Route::post('/produk-add', [ProdukController::class, 'insert'])->name('produk.insert');
Route::delete('/delete-produk', [ProdukController::class, 'destroy'])->name('produk.delete');

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


/*
    Member Route
*/
Route::delete('member', [MemberController::class, 'destroy'])->name('member.delete');
Route::post('member', [MemberController::class, 'insert'])->name('member.insert');

/*
    Datatable ajx route
*/
Route::get('/get-produk', [ProdukController::class, 'get'])->name('produk.get');
Route::get('/get-member', [MemberController::class, 'get'])->name('member.get');
