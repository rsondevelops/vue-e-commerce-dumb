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
            $table->string('order_code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Snapshot data pengiriman saat checkout
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address');

            $table->enum('status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])
                  ->default('pending');
            $table->enum('payment_method', ['transfer', 'cod']);
            $table->unsignedBigInteger('total_amount');
            $table->text('notes')->nullable();
            $table->text('cancelled_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};