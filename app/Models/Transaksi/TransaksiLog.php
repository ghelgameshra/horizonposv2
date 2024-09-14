<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiLog extends Model
{
    use HasFactory;
    protected $table = 'transaksi_log';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_transaksi',
        'namafile',
        'plu',
        'nama_produk',
        'id_kategori',
        'harga_jual',
        'jumlah',
        'satuan',
        'harga_ukuran',
        'ukuran',
        'total',
        'informasi_stok',
        'status_order',
    ];
}
