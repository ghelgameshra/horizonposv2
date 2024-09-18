<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    use HasFactory;
    protected $table = 'toko';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_perusahaan',
        'email',
        'telepone',
        'whatsapp',
        'instagram',
        'facebook',
        'tiktok',
        'web',
        'alamat_lengkap',
        'logo',
        'qr_wa',
    ];
}
