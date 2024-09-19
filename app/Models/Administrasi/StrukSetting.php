<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrukSetting extends Model
{
    use HasFactory;
    protected $table = 'setting_struk';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
