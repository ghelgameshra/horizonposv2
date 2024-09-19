<?php

namespace App\Models\Transaksi;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';
    protected $primaryKey = 'id';
    protected $fillable = [
        'invno',
        'id_member',
        'nama_customer',
        'kode_promo',
        'nomor_telepone',
        'tanggal_transaksi',
        'subtotal',
        'diskon',
        'total',
        'uang_muka',
        'terima',
        'kembali',
        'tipe_bayar',
        'tipe_bayar_pelunasan',
        'kasir_id',
        'status_order',
        'addid',
    ];

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id', 'id');
    }

    public function transaksiLog(): HasMany
    {
        return $this->hasMany(TransaksiLog::class, 'id_transaksi', 'id');
    }
}
