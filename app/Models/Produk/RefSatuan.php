<?php

namespace App\Models\Produk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefSatuan extends Model
{
    use HasFactory;
    protected $table = 'ref_satuan';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
