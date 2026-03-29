<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->nullable(); // Ödeme sağlayıcı ID (PayTR, Stripe, vb.)
            $table->string('payment_method'); // credit_card, havale, kapida_odeme
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('TRY');
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->text('error_message')->nullable();
            $table->json('response_data')->nullable(); // API yanıtı
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};