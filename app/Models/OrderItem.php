<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'urun_id', 'urun_variant_id',
        'product_name', 'variant_name', 'sku',
        'unit_price', 'unit_discount_price', 'quantity',
        'subtotal', 'total',
        'return_status', 'returned_quantity', 'return_reason'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(UrunVariant::class, 'urun_variant_id');
    }

    // Yorum İlişkisi: Bir sipariş kaleminin bir yorumu olur
    public function review(): HasOne
    {
        // reviews tablosundaki order_item_id sütunu ile bağlanır
        return $this->hasOne(Review::class, 'order_item_id');
    }
}