<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = 'karyawan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_lengkap',
        'nik',
        'jabatan',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat_domisili',
        'telepone',
        'jobdesk',
        'agama',
        'ktp',
        'status_pernikahan',
        'pendidikan_terakhir',
        'tanggal_masuk',
    ];
}
