<?php

use App\Http\Controllers\FrontEnd\FeController;
use Illuminate\Support\Facades\Route;

Route::get('produk', [FeController::class, 'produk'])->name('produk.index');
