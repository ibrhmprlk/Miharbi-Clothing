<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'urun_variant_id',
        'quantity'
    ];

    /**
     * Kullanıcı ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
public function urun() {
    return $this->belongsTo(Urun::class, 'urun_id');
}
    /**
     * Ürün varyantı ilişkisi
     */
    public function urunVariant(): BelongsTo
    {
        return $this->belongsTo(UrunVariant::class, 'urun_variant_id');
    }
}