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
    {
        Schema::create('abouts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
           // Metin içerikleri
    $table->string('title');
    $table->text('description');
    $table->text('second_paragraph')->nullable();
    $table->text('last_paragraph')->nullable();

    // Görsel
    $table->string('image')->nullable();

    // İletişim bilgileri
    $table->string('phone')->nullable();
    $table->string('email')->nullable();

    // Sosyal medya
    $table->string('instagram_url')->nullable();
    $table->string('twitter_url')->nullable();
   $table->string('github_url')->nullable();
    $table->string('linkedin_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};