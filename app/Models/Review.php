<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'urun_id',
        'urun_variant_id',
        'order_item_id',
        'rating',           // Sadece bu kaldı
        'comment',
        'is_approved',
        'purchased_at'
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'is_approved' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(UrunVariant::class, 'urun_variant_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}