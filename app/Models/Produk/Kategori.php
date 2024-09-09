<?php

namespace App\Models\Produk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    protected $table = 'ref_kategori';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'addid'
    ];
}
