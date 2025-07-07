<?php

namespace App\Models\Kasir;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromosiProduk extends Model
{
    use HasFactory;
    protected $table = 'promosi_produk';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
