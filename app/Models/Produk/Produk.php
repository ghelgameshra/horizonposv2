<?php

namespace App\Models\Produk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'produk';
    protected $primaryKey = 'id';
    protected $fillable = [
        'plu',
        'nama_produk',
        'id_kategori',
        'harga_beli',
        'harga_jual',
        'merk',
        'stok',
        'addno',
        'jenis_ukuran',
        'satuan',
        'plu_aktif',
        'jual_minus',
        'retur',
        'kode_suppllier',
        'addid',
    ];
}
