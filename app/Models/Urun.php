<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Urun extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
 
        'slug',
        'description',
        'is_active',
    ];

    // Category ilişkisi
    public function category()
    {
        return $this->belongsTo(Category::class);
        
    }
//Urun extends Model demek,Urun sınıfının Model sınıfındaki tüm veritabanı yeteneklerini kullanabilmesi demektir.

// Variant ilişkisi
    public function variants()
    {
        return $this->hasMany(UrunVariant::class);
    }
    // ACCESSOR: minimum fiyat
    public function getMinPriceAttribute()
    {
        return $this->variants()->min('price');
    }
    // Ürün görselleri
    public function images()
    {
        return $this->hasMany(UrunImage::class);
    }
    public function firstImage()
{
    return $this->hasOne(UrunImage::class)->latestOfMany();
}
}