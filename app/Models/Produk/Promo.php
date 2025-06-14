<?php

namespace App\Models\Produk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;
    protected $table = 'promosi';
    protected $primaryKey = 'id';
    protected $guarded = [];

    protected $promoMember = false;

    // Mutator untuk nama_promosi
    public function setNamaPromoAttribute($value)
    {
        $this->attributes['nama_promo'] = strtoupper($value);
    }

    // Mutator untuk detail_promosi
    public function setDetailPromoAttribute($value)
    {
        $this->attributes['detail_promo'] = strtoupper($value);
    }

    // Mutator untuk tipe_diskon
    public function setTipePromoAttribute($value)
    {
        if($value === "MEMBER") {
            $this->promoMember = true;
        }
        $this->attributes['tipe_promo'] = strtoupper($value);
        $this->attributes['promo_member'] = $this->promoMember;
    }
}
