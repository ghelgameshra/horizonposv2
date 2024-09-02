<?php

use App\Http\Controllers\Produk\ProdukController;
use Illuminate\Support\Facades\Route;

Route::post('/produk', [ProdukController::class, 'upsert'])->name('produk.upsert');
Route::get('/get-produk', [ProdukController::class, 'get'])->name('produk.get');
Route::delete('/delete-produk', [ProdukController::class, 'destroy'])->name('produk.delete');
