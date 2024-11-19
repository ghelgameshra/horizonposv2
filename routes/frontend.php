<?php

use App\Http\Controllers\FrontEnd\FeController;
use Illuminate\Support\Facades\Route;

Route::get('master/produk', [FeController::class, 'produk'])->name('produk.index');
Route::get('kasir', [FeController::class, 'kasir'])->name('kasir.index');
Route::get('master/member', [FeController::class, 'member'])->name('member.index');
Route::get('master/promosi', [FeController::class, 'promosi'])->name('promosi.index');
Route::get('master/karyawan', [FeController::class, 'karyawan'])->name('karyawan.index');
Route::get('pengaturan/toko', [FeController::class, 'toko'])->name('toko.index');
Route::get('pengaturan/printer', [FeController::class, 'printer'])->name('printer.index');
Route::get('transaksi/pelunasan', [FeController::class, 'pelunasan'])->name('pelunasan.index');
Route::get('transaksi/reprint-transaksi', [FeController::class, 'reprintTrx'])->name('transaksi.reprint');
Route::get('pengeluaran', [FeController::class, 'pengeluaran'])->name('pengeluaran.index');
Route::get('pesanan/pengambilan', [FeController::class, 'pengambilan'])->name('pengambilan.index');
Route::get('pesanan/work-order', [FeController::class, 'workOrder'])->name('workOrder.index');
