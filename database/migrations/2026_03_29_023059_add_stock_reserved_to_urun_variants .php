<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('urun_variants', function (Blueprint $table) {
            $table->integer('stock_reserved')->default(0)->after('stock');
        });
    }

    public function down(): void
    {
        Schema::table('urun_variants', function (Blueprint $table) {
            $table->dropColumn('stock_reserved');
        });
    }
};
