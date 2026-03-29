<?php
// app/Models/Order.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'user_id', 'guest_email', 'guest_phone',
        'shipping_full_name', 'shipping_phone', 'shipping_address',
        'shipping_city', 'shipping_district', 'shipping_zip',
        'same_as_shipping', 'billing_full_name', 'billing_address',
        'payment_method', 'payment_status', 'paid_at',
        'status', 'shipping_company', 'tracking_number',
        'shipped_at', 'delivered_at',
        'subtotal', 'shipping_cost', 'discount_amount', 'tax_amount', 'total',
        'customer_note', 'admin_note'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'same_as_shipping' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}