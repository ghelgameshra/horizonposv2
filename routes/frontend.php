<?php

use App\Http\Controllers\FrontEnd\FeController;
use Illuminate\Support\Facades\Route;

Route::get('master/produk', [FeController::class, 'produk'])->name('produk.index');
Route::get('kasir', [FeController::class, 'kasir'])->name('kasir.index');
Route::get('master/member', [FeController::class, 'member'])->name('member.index');
Route::get('master/promosi', [FeController::class, 'promosi'])->name('promosi.index');
Route::get('master/karyawan', [FeController::class, 'karyawan'])->name('karyawan.index');
