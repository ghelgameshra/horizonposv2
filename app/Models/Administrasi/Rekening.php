<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;
    protected $table = 'ref_rekening';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
