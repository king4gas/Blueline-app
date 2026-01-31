<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
    {
        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            // Relasi ke order dan user
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Sesuai Flowchart Kanan: Butuh Bukti & Keterangan
            $table->string('reason'); // Keterangan Masalah
            $table->string('image');  // Bukti Foto (Path file)
            $table->string('video')->nullable(); // Bukti Video (Opsional)
            
            // Status: pending (menunggu admin), approved (kirim barang), rejected (ditolak), completed
            $table->string('status')->default('pending'); 
            $table->text('admin_note')->nullable(); // Alasan jika ditolak admin
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_returns');
    }
};
