<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeController extends Controller
{
    public function produk(): View
    {
        return view('pages.produk.produk');
    }

    public function kasir(): View
    {
        return view('pages.kasir.index');
    }

    public function member(): View
    {
        return view('pages.member.member');
    }

    public function promosi(): View
    {
        return view('pages.promosi.promosi');
    }

    public function karyawan(): View
    {
        return view('pages.karyawan.karyawan');
    }
}
