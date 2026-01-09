<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Router Mikrotik" atau "Paket Internet 50Mbps"
            $table->string('slug')->unique(); // Untuk URL ramah SEO
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // Harga
            
            // Membedakan apakah ini barang fisik atau layanan
            $table->enum('type', ['hardware', 'service']); 
            
            // Jika hardware, butuh stok. Jika layanan, stok tidak relevan (null)
            $table->integer('stock')->nullable(); 
            
            // Kecepatan (khusus layanan internet), misal: "50 Mbps"
            $table->string('speed')->nullable(); 
            
            $table->string('image')->nullable(); // Foto produk
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
