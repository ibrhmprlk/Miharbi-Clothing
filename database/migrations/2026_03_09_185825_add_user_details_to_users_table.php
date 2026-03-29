<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->date('birth_date')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birth_date');
            
            // Varsayılan adres bilgileri (hızlı checkout için)
            $table->string('default_address_title')->nullable()->after('gender'); // Ev, İşyeri, vb.
            $table->text('default_address')->nullable()->after('default_address_title');
            $table->string('default_city')->nullable()->after('default_address');
            $table->string('default_district')->nullable()->after('default_city');
            $table->string('default_zip')->nullable()->after('default_district');
            
             $table->boolean('is_active')->default(true)->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'birth_date', 'gender', 
                'default_address_title', 'default_address', 
                'default_city', 'default_district', 'default_zip',
                'is_active'
            ]);
        });
    }
};