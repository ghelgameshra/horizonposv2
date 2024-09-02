<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeController extends Controller
{
    public function produk()
    {
        return view('pages.produk.produk');
    }
}
