<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrunImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'urun_id',
        'image_url',
    ];

    public function urun()
    {
        return $this->belongsTo(Urun::class);
    }
}