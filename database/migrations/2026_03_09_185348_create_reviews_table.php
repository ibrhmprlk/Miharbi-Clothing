<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            
            // İlişkiler
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('urun_id')->constrained('uruns')->onDelete('cascade');
            $table->foreignId('urun_variant_id')->nullable()->constrained('urun_variants')->onDelete('set null');
            
            // Order item ilişkisi
            $table->unsignedBigInteger('order_item_id');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            
            // Sadece ana puanlama
            $table->tinyInteger('rating')->unsigned();
            
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamp('purchased_at')->nullable();
            
            $table->timestamps();
            
            $table->unique(['order_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};