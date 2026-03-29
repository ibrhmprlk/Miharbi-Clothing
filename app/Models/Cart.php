<?php
// app/Models/Cart.php (Eğer yoksa)
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'urun_id', 'urun_variant_id', 'quantity'];

    public function urun(): BelongsTo
    {
        return $this->belongsTo(Urun::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(UrunVariant::class, 'urun_variant_id');
    }
}