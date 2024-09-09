<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $table = 'member';
    protected $primaryKey = 'id';
    protected $fillable = [
        'kode_member',
        'nama_lengkap',
        'alamat_lengkap',
        'telepone',
        'email',
    ];
}
