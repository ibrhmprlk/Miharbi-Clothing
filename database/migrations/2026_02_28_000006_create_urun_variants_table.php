<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{Schema::create('urun_variants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('urun_id')->constrained()->onDelete('cascade');
    $table->string('color')->nullable();
    $table->string('color_code')->nullable();
    $table->string('size')->nullable();
    $table->decimal('price', 10, 2);
    $table->decimal('discount_price', 10, 2)->nullable();
    $table->integer('stock')->default(0);
    $table->string('sku')->unique()->nullable(); 
    
    // YENİ EKLENENLER: Artık her varyantın kendi markası, koleksiyonu ve aktifliği var
    $table->string('brand')->nullable();
    $table->string('collection')->nullable();
    $table->boolean('is_active')->default(true); 
    
    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urun_variants');
    }
};
