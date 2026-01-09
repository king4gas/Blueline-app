<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        // 1. Tabel Keranjang (Cart)
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        // 2. Tabel Pesanan Utama (Order Header)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('total_price', 15, 2);
            $table->string('status')->default('pending_verification'); // pending, verified, rejected, completed
            $table->string('payment_proof')->nullable(); // Path foto bukti transfer
            $table->text('address');
            $table->string('phone');
            $table->timestamps();
        });

        // 3. Tabel Detail Item Pesanan (Order Items)
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained(); // Jangan cascade delete agar history aman
            $table->string('product_name'); // Snapshot nama produk saat beli
            $table->decimal('price', 15, 2); // Snapshot harga saat beli
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('carts');
    }
};
