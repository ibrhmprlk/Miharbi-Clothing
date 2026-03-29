<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyOrder extends Model
{
    use HasFactory;

    // Veritabanındaki tablo adını sabitledik
    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'guest_email',
        'guest_phone',
        'shipping_full_name',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_district',
        'shipping_zip',
        'same_as_shipping',
        'billing_full_name',
        'billing_address',
        'payment_method',
        'payment_status',
        'paid_at',
        'status',
        'shipping_company',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'subtotal',
        'shipping_cost',
        'discount_amount',
        'tax_amount',
        'total', // Migration dosmanda 'total_amount' değil 'total' yazıyor
        'customer_note',
        'admin_note',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // --- İLİŞKİLER ---

    public function user()
    {
        // Yabancı anahtarı açıkça belirttik
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        // Migration'da order_id yazdığı için buraya 'order_id' ekledik
        return $this->hasMany(OrderItem::class, 'order_id');
    }

 

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending'   => ['text' => 'Beklemede', 'class' => 'badge-amber'],
            'confirmed' => ['text' => 'Onaylandı', 'class' => 'badge-blue'],
            'processing'=> ['text' => 'Hazırlanıyor', 'class' => 'badge-indigo'],
            'shipped'   => ['text' => 'Kargoda', 'class' => 'badge-blue'],
            'delivered' => ['text' => 'Teslim Edildi', 'class' => 'badge-emerald'],
            'cancelled' => ['text' => 'İptal Edildi', 'class' => 'badge-red'],
            'returned'  => ['text' => 'İade Edildi', 'class' => 'badge-gray'],
        ];

        return $badges[$this->status] ?? ['text' => $this->status, 'class' => 'badge-gray'];
    }

    public function getPaymentMethodTextAttribute()
    {
        $methods = [
            'credit_card'      => 'Kredi Kartı',
            'bank_transfer'    => 'Havale/EFT',
            'cash_on_delivery' => 'Kapıda Ödeme',
            'wallet'           => 'Cüzdan',
        ];

        return $methods[$this->payment_method] ?? $this->payment_method;
    }
}