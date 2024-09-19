<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrinterSetting extends Model
{
    use HasFactory;
    protected $table = 'setting_printer';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_printer',
        'jenis_printer',
        'protocol_printer',
        'ip_printer',
        'port_printer',
        'username_printer',
        'password_printer',
        'default_printer',
        'keterangan_printer',
    ];
}
