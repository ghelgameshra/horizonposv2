<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FeController extends Controller
{
    public function produk(): View
    {
        return view('pages.produk.produk');
    }

    /*
        Redirect ke halaman tutup harian ketika sudah tutup harian
    */
    public function kasir()
    {
        $tutupHarian = DB::table('tutup_harian')->whereDate('tanggal_harian', now()->format('Y-m-d'))->first();
        if($tutupHarian){
            return redirect()->route('tutupHarian.index');
        }

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

    public function toko(): View
    {
        return view('pages.toko.setting');
    }

    public function printer(): View
    {
        return view('pages.printer.index');
    }

    public function pelunasan(): View
    {
        return view('pages.pelunasan.index');
    }

    public function reprintTrx(): View
    {
        return view('pages.transaksi.index');
    }

    public function pengeluaran(): View
    {
        return view('pages.pengeluaran.index');
    }

    public function pengambilan(): View
    {
        return view('pages.pesanan.index');
    }

    public function workOrder(): View
    {
        return view('pages.work-order.index');
    }

    public function tutupHarian(): View
    {
        return view('pages.tutup-harian.index');
    }
}
