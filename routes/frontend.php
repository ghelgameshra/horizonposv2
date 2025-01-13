<?php

use App\Http\Controllers\FrontEnd\FeController;
use Illuminate\Support\Facades\Route;

Route::get('master/produk', [FeController::class, 'produk'])->name('produk.index')->middleware('permission:show produk');
Route::get('kasir', [FeController::class, 'kasir'])->name('kasir.index')->middleware('permission:show kasir');
Route::get('master/member', [FeController::class, 'member'])->name('member.index')->middleware('permission:show member');
Route::get('master/promosi', [FeController::class, 'promosi'])->name('promosi.index')->middleware('permission:show promosi');
Route::get('master/karyawan', [FeController::class, 'karyawan'])->name('karyawan.index')->middleware('permission:show karyawan');
Route::get('pengaturan/toko', [FeController::class, 'toko'])->name('toko.index')->middleware('permission:show toko');
Route::get('pengaturan/printer', [FeController::class, 'printer'])->name('printer.index')->middleware('permission:show printer');
Route::get('transaksi/pelunasan', [FeController::class, 'pelunasan'])->name('pelunasan.index')->middleware('permission:show pelunasan');
Route::get('transaksi/reprint-transaksi', [FeController::class, 'reprintTrx'])->name('transaksi.reprint')->middleware('permission:show reprint');
Route::get('pengeluaran', [FeController::class, 'pengeluaran'])->name('pengeluaran.index')->middleware('permission:show pengeluaran');
Route::get('pesanan/pengambilan', [FeController::class, 'pengambilan'])->name('pengambilan.index')->middleware('permission:show ambil pesanan');
Route::get('pesanan/work-order', [FeController::class, 'workOrder'])->name('workOrder.index')->middleware('permission:show work order');
Route::get('tutup-harian', [FeController::class, 'tutupHarian'])->name('tutupHarian.index')->middleware('permission:show tutup harian');
Route::get('pengaturan/user', [FeController::class, 'user'])->name('user.index')->middleware('permission:show user');

Route::get('user-permission-failed', function(){
    return view('pages.forbidden.index');
})->name('user.permission.failed');
