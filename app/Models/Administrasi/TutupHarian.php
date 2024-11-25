<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutupHarian extends Model
{
    use HasFactory;
    protected $table = 'tutup_harian';
    protected $primaryKey = 'id';
    protected $fillable = [
        'invno',
        'tanggal_harian',
        'rp100000',
        'rp75000',
        'rp50000',
        'rp20000',
        'rp10000',
        'rp5000',
        'rp1000',
        'rp2000',
        'rp500',
        'rp200',
        'rp100',
        'rptotal',
        'user'
    ];
}
