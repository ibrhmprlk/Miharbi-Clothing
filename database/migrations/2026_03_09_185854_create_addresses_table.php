<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Ev, İş, Yazlık, vb.
            $table->string('full_name'); // Teslim alacak kişi
            $table->string('phone');
            $table->text('address'); // Açık adres
            $table->string('city');
            $table->string('district'); // İlçe
            $table->string('neighborhood')->nullable(); // Mahalle
            $table->string('zip_code')->nullable();
            $table->boolean('is_default')->default(false); // Varsayılan adres mi?
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};