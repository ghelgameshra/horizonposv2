<?php

use App\Http\Controllers\FrontEnd\FeController;
use Illuminate\Support\Facades\Route;

Route::get('produk', [FeController::class, 'produk'])->name('produk.index');
Route::get('kasir', [FeController::class, 'kasir'])->name('kasir.index');
Route::get('member', [FeController::class, 'member'])->name('member.index');
