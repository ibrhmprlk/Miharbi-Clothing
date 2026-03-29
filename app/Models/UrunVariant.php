<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrunVariant extends Model
{
    use HasFactory;

protected $fillable = [
    'urun_id',
    'color',
    'color_code',
    'size',
    'price',
    'discount_price',
    'stock',
    'sku',
    'brand',       // Eklendi
    'collection',  // Eklendi
    'is_active',   // Eklendi
];

    public function urun()
    {
        return $this->belongsTo(Urun::class);
    }

    // Her varyantın kendi görselleri için
    public function images()
    {
        return $this->hasMany(UrunImage::class, 'variant_id'); 
        // UrunVariantImage adını kendi modeline göre ayarla, tablo variant_id kolonuna sahip olmalı
    }
}