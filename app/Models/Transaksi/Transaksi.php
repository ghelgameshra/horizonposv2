<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';
    protected $primaryKey = 'id';
    protected $fillable = [
        'invno',
        'id_member',
        'nama_customer',
        'kode_promo',
        'nomor_telepone',
        'tanggal_transaksi',
        'subtotal',
        'diskon',
        'total',
        'uang_muka',
        'terima',
        'kembali',
        'tipe_bayar',
        'kasir_id',
        'status_order',
        'addid',
    ];
}
