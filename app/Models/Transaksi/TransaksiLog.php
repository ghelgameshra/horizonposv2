<?php

namespace App\Models\Transaksi;

use App\Models\Produk\Kategori;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiLog extends Model
{
    use HasFactory;
    protected $table = 'transaksi_log';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_transaksi',
        'namafile',
        'plu',
        'nama_produk',
        'id_kategori',
        'harga_jual',
        'jumlah',
        'satuan',
        'harga_ukuran',
        'ukuran',
        'total',
        'informasi_stok',
        'worker',
        'worker_addid',
        'status_order',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id');
    }
}
