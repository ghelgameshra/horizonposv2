<?php

namespace App\Models\Kasir;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromosiLarangan extends Model
{
    use HasFactory;
    protected $table = 'promosi_plu_larangan';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
