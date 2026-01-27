<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('return_reason')->nullable();   // Alasan: Rusak, Salah Barang, dll
            $table->string('return_evidence')->nullable(); // Path Foto Bukti
            $table->text('rejection_reason')->nullable();  // Alasan Admin menolak (opsional)
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['return_reason', 'return_evidence', 'rejection_reason']);
        });
    }
};
