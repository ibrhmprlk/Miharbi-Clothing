<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne; // Eklendi
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Eklendi
use App\Models\Address;
use Laravel\Cashier\Billable;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
use HasFactory, Notifiable, Billable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function favorites()
    {
        return $this->belongsToMany(UrunVariant::class, 'favorites')
                    ->withTimestamps();
    }
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function addresses() 
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    // DÜZELTİLEN KISIM: Kullanıcının yorumu (Genelde hasMany olur ama tekil istediysen hasOne)
    public function review(): HasOne
    {
        // User_id üzerinden Review tablosuna bağlanır
        return $this->hasOne(Review::class, 'user_id');
    }

    // DÜZELTİLEN KISIM: Kullanıcı bir siparişe ait olamaz, sipariş kullanıcıya aittir.
    // Eğer tekil bir sipariş çekmek istiyorsan hasOne kullanılır.
    public function order(): HasOne
    {
        return $this->hasOne(Order::class)->latestOfMany();
    }
}