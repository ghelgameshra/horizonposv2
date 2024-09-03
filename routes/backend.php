<?php

use App\Http\Controllers\Produk\ProdukController;
use App\Http\Controllers\Select2\Select2Controller;
use Illuminate\Support\Facades\Route;

Route::post('/produk', [ProdukController::class, 'upsert'])->name('produk.upsert');
Route::post('/produk-add', [ProdukController::class, 'insert'])->name('produk.insert');
Route::get('/get-produk', [ProdukController::class, 'get'])->name('produk.get');
Route::delete('/delete-produk', [ProdukController::class, 'destroy'])->name('produk.delete');

/*
    Select2 Route
*/
Route::get('select2-jenis-ukuran', [Select2Controller::class, 'select2JenisUkuran'])->name('select2JenisUkuran');
Route::get('select2-kategori-produk', [Select2Controller::class, 'select2JenisKategori'])->name('select2JenisKategori');
Route::get('select2-satuan-produk', [Select2Controller::class, 'select2JenisSatuan'])->name('select2JenisSatuan');
