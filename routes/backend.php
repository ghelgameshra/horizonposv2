<?php

use App\Http\Controllers\Administrasi\AntrianController;
use App\Http\Controllers\Administrasi\DashboardController;
use App\Http\Controllers\Administrasi\KaryawanController;
use App\Http\Controllers\Administrasi\MemberController;
use App\Http\Controllers\Administrasi\PengeluaranController;
use App\Http\Controllers\Administrasi\PrinterController;
use App\Http\Controllers\Administrasi\RekeningController;
use App\Http\Controllers\Administrasi\StrukController;
use App\Http\Controllers\Administrasi\TokoController;
use App\Http\Controllers\Administrasi\TutupHarianController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Order\WorkOrderController;
use App\Http\Controllers\Produk\KategoriController;
use App\Http\Controllers\Produk\ProdukController;
use App\Http\Controllers\Produk\PromoController;
use App\Http\Controllers\Produk\SatuanJualController;
use App\Http\Controllers\Select2\Select2Controller;
use App\Http\Controllers\Struk\TestPrinterController;
use App\Http\Controllers\Transaksi\PelunasanController;
use App\Http\Controllers\Transaksi\TransaksiController;
use Illuminate\Support\Facades\Route;

/*
    Produk route
*/
Route::post('/produk', [ProdukController::class, 'upsert'])->name('produk.upsert');
Route::get('/produk/{plu?}', [ProdukController::class, 'data'])->name('produk.data');
Route::post('/produk-add', [ProdukController::class, 'insert'])->name('produk.insert');
Route::put('/produk-add/{plu?}', [ProdukController::class, 'update'])->name('produk.update');
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
Route::get('select2-jenis-pengeluaran', [Select2Controller::class, 'select2JenisPengeluaran'])->name('select2JenisPengeluaran');


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
Route::get('karyawan-export', [KaryawanController::class, 'export'])->name('karyawan.export');
Route::post('karyawan', [KaryawanController::class, 'upsert'])->name('karyawan.upsert');
Route::post('karyawan-baru', [KaryawanController::class, 'insert'])->name('karyawan.insert');
Route::delete('karyawan', [KaryawanController::class, 'delete'])->name('karyawan.delete');

/*
    Kasir Route
*/
Route::get('kasir/transaksi-baru-detail', [TransaksiController::class, 'transaksiBaruDetail'])->name('transaksiBaruDetail');
Route::post('kasir/transaksi-baru/{user?}', [TransaksiController::class, 'transaksiBaru'])->name('transaksiBaru');
Route::post('kasir/transaksi-baru-log', [TransaksiController::class, 'transaksiLog'])->name('transaksiLog');
Route::delete('kasir/transaksi-baru-log/{id?}', [TransaksiController::class, 'transaksiLogDelete'])->name('transaksiLogDelete');
Route::put('kasir/transaksi-tambah-qty/{id?}/{plu?}/{qty?}', [TransaksiController::class, 'tambahQty'])->name('tambahQty');
Route::put('kasir/transaksi-addfilesize/{id_transaksi_log?}', [TransaksiController::class, 'addfilesize'])->name('addfilesize');
Route::post('kasir/transaksi-cek-promo', [TransaksiController::class, 'cekPromo'])->name('cekPromo');
Route::put('kasir/transaksi-baru', [TransaksiController::class, 'transaksiSelesai'])->name('transaksiSelesai');

/*
    Pengaturan toko route
*/
Route::get('toko', [TokoController::class, 'get'])->name('toko.get');
Route::get('toko/pesan-struk', [StrukController::class, 'getPesanStruk'])->name('getPesanStruk');
Route::put('toko', [TokoController::class, 'update'])->name('toko.update');
Route::put('toko/change-logo', [TokoController::class, 'changeLogo'])->name('toko.changeLogo');


/*
    Printer route
*/

Route::get('printer', [PrinterController::class, 'printer'])->name('printer.get');
Route::post('printer', [PrinterController::class, 'insert'])->name('printer.insert');
Route::delete('printer', [PrinterController::class, 'destroy'])->name('printer.delete');
Route::get('printer-struk-setting', [StrukController::class, 'getPrinterSetting'])->name('getPrinterSetting');
Route::put('printer-struk-setting', [StrukController::class, 'changeStatus'])->name('printer.changeStatus');
Route::get('test-print', [TestPrinterController::class, 'test'])->name('printer.test');


/*
    Pelunasan route
*/
Route::get('pelunasan', [PelunasanController::class, 'detail'])->name('pelunasan.detail');
Route::put('pelunasan', [PelunasanController::class, 'update'])->name('pelunasan.update');

/*
    Reprint route
*/
Route::get('reprint-transaksi/{invno?}', [StrukController::class, 'reprint'])->name('reprint-transaksi.print');
Route::get('transaksi/{invno?}', [TransaksiController::class, 'show'])->name('pengeluaran.show');
Route::put('pesanan/pengambilan/{invno?}', [TransaksiController::class, 'ambil'])->name('pesanan.ambil');
Route::post('transaksi/{invno?}', [TransaksiController::class, 'cancel'])->name('transaksi.cancel');

/*
    Pengeluaran route
*/
Route::post('jenis-pengeluaran', [PengeluaranController::class, 'insertJenis'])->name('jenis-pengeluaran.insert');
Route::post('pengeluaran', [PengeluaranController::class, 'insert'])->name('pengeluaran.insert');
Route::delete('pengeluaran', [PengeluaranController::class, 'destroy'])->name('pengeluaran.delete');
Route::post('upload-pengeluaran-ref', [PengeluaranController::class, 'uploadRef'])->name('pengeluaran.uploadRef');


/*
    Datatable ajx route
*/
Route::get('list-transaksi', [TransaksiController::class, 'getTransaksi'])->name('transaksi.list');
Route::get('get-produk', [ProdukController::class, 'get'])->name('produk.get');
Route::get('get-member', [MemberController::class, 'get'])->name('member.get');
Route::get('get-promo', [PromoController::class, 'get'])->name('promo.get');
Route::get('get-karyawan', [KaryawanController::class, 'get'])->name('karyawan.get');
Route::get('get-produk-jual', [TransaksiController::class, 'getProdukJual'])->name('getProdukJual');
Route::get('get-transaksi-pending', [PelunasanController::class, 'get'])->name('pelunasan.get');
Route::get('get-pengeluaran', [PengeluaranController::class, 'get'])->name('pengeluaran.get');


/*
    Dashboard data
*/
Route::get('dashboard-data', [DashboardController::class, 'data'])->name('dashboard.data');
Route::get('antrian-customer', [AntrianController::class, 'data'])->name('antrian.data');
Route::get('antrian-customer-repeat', [AntrianController::class, 'repeat'])->name('antrian.repeat');
Route::post('antrian-customer', [AntrianController::class, 'create'])->name('antrian.create');
Route::put('antrian-customer', [AntrianController::class, 'update'])->name('antrian.update');


/*
    Work Order Route
 */
Route::get('work-order/data/{categoryId?}/{progress?}', [WorkOrderController::class, 'data'])->name('workOrder.data');
Route::get('status-order', [WorkOrderController::class, 'statusOrder'])->name('statusOrder');
Route::put('work-order/next-progress/{id?}/{nextProgress?}', [WorkOrderController::class, 'updateProgress'])->name('workOrder.updateProgress');

/*
    Rekening Route
*/
Route::get('rekening-list', [RekeningController::class, 'data'])->name('data.rekening');
Route::get('rekening', [RekeningController::class, 'defaultRekening'])->name('get.defaultRekening');
Route::post('rekening', [RekeningController::class, 'store'])->name('create.rekening');
Route::put('rekening/{nomorRekening?}', [RekeningController::class, 'update'])->name('update.rekening');
Route::delete('rekening/{nomorRekening?}', [RekeningController::class, 'destroy'])->name('delete.rekening');


/*
    Route Tutup Harian
*/
Route::get('data-tutup-harian', [TutupHarianController::class, 'data'])->name('data.tutupHarian');
Route::post('tutup-harian', [TutupHarianController::class, 'store'])->name('create.tutupHarian');
Route::get('check-tutup-harian', [TutupHarianController::class, 'checkHarian'])->name('check.tutupHarian');

Route::get('user', [UserController::class, 'get'])->name('get.user');
Route::put('user-table/{id?}', [UserController::class, 'changeTable'])->name('changeTable.user');
Route::get('role-permission/{roleId?}', [UserController::class, 'rolePermission'])->name('get.rolePermission');
Route::put('role-permission/{roleId?}', [UserController::class, 'updatePermissions'])->name('updatePermissions');
Route::put('user-role/{userId?}', [UserController::class, 'updateUserRole'])->name('updateUserRole');

/*
    Setting satuan jual
*/
Route::get('produk-satuan-jual', [SatuanJualController::class, 'data'])->name('satuanJual.data');
Route::post('produk-satuan-jual', [SatuanJualController::class, 'insert'])->name('satuanJual.insert');
Route::delete('produk-satuan-jual/{id?}', [SatuanJualController::class, 'destroy'])->name('satuanJual.destroy');
Route::put('produk-satuan-jual/update-status/{id?}/{namaConfig?}', [SatuanJualController::class, 'updateConfig'])->name('satuanJual.updateConfig');
