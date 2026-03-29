<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // Ürün bilgileri (fiyat değişse bile sipariş anındaki değerler kalsın)
            $table->foreignId('urun_id')->constrained('uruns')->onDelete('restrict');
            $table->foreignId('urun_variant_id')->constrained('urun_variants')->onDelete('restrict');
            
            // Sipariş anındaki değerler (kopya)
            $table->string('product_name'); // O anki ürün adı
            $table->string('variant_name')->nullable(); // "Kırmızı - M Beden" gibi
            $table->string('sku')->nullable(); // O anki SKU
            $table->decimal('unit_price', 10, 2); // Birim fiyat
            $table->decimal('unit_discount_price', 10, 2)->nullable(); // İndirimli fiyat
            $table->integer('quantity');
            
            // Toplam (quantity * price)
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            
            // İade bilgisi
            $table->enum('return_status', ['none', 'requested', 'approved', 'rejected', 'completed'])->default('none');
            $table->integer('returned_quantity')->default(0);
            $table->text('return_reason')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};