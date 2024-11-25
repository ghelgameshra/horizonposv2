<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutupHarianDetail extends Model
{
    use HasFactory;
    protected $table = 'tutup_harian_detail';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_harian',
        'jumlah_sales',
        'pesanan_selesai',
        'pesanan_cancel',
        'total_nominal_sales',
        'jumlah_sales_dikson',
        'total_nominal_diskon',
        'jumlah_bayar_dpcsh',
        'total_bayar_dpcsh',
        'jumlah_bayar_csh',
        'total_bayar_csh',
        'jumlah_bayar_dptrf',
        'total_bayar_dptrf',
        'jumlah_bayar_trf',
        'total_bayar_trf',
        'piutang',
        'rptotal',
        'selisih_fisik',
    ];
}
