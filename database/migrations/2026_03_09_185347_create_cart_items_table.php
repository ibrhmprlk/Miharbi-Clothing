<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('urun_variant_id')->constrained('urun_variants')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();
            
            // Her kullanıcı her varyantı sepette sadece bir kez tutar (quantity artar)
            $table->unique(['user_id', 'urun_variant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};