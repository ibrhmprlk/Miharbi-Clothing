<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // ORD-2024-000001 gibi
            
            // Kullanıcı bilgileri (kayıtlı kullanıcı veya misafir)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('guest_email')->nullable(); // Misafir kullanıcı için
            $table->string('guest_phone')->nullable();
            
            // Fatura/Teslimat adresi (kopya - adres silinse bile siparişte kalsın)
            $table->string('shipping_full_name');
            $table->string('shipping_phone');
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_district');
            $table->string('shipping_zip')->nullable();
            
            // Fatura adresi (farklıysa)
            $table->boolean('same_as_shipping')->default(true);
            $table->string('billing_full_name')->nullable();
            $table->text('billing_address')->nullable();
            
            // Ödeme bilgileri
            $table->enum('payment_method', ['credit_card', 'bank_transfer', 'cash_on_delivery', 'wallet']);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            
            // Sipariş durumu (Trendyol tarzı)
            $table->enum('status', [
                'pending',      // Beklemede
                'confirmed',    // Onaylandı
                'processing',   // Hazırlanıyor
                'shipped',      // Kargoya verildi
                'delivered',    // Teslim edildi
                'cancelled',    // İptal edildi
                'returned'      // İade edildi
            ])->default('pending');
            
            // Kargo bilgileri
            $table->string('shipping_company')->nullable(); // Aras, Yurtiçi, vb.
            $table->string('tracking_number')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            // Fiyatlandırma
            $table->decimal('subtotal', 12, 2); // Ürün toplamı
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2); // Genel toplam
            
            // Notlar
            $table->text('customer_note')->nullable(); // Müşteri notu
            $table->text('admin_note')->nullable(); // Admin notu
            
            $table->timestamps();
            
            // İndeksler
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};