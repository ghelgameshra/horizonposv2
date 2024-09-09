<?php

namespace App\Models\Produk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;
    protected $table = 'promo';
    protected $primaryKey = 'id';
    protected $fillable = [
        'kode_promo',
        'item_syarat',
        'promo_member',
        'minimal_belanja',
        'maksimal_belanja',
        'potongan_persentase',
        'potongan_langsung',
        'potongan_minimal',
        'potongan_maksimal',
        'jenis_promo',
        'mekanisme_promo',
        'periode_awal',
        'periode_akhir',
        'total_penggunaan',
        'addid',
    ];
}
