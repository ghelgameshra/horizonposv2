<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstApp extends Model
{
    use HasFactory;
    protected $table = 'const';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
