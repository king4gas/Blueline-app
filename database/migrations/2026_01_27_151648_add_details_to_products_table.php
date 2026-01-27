<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            
            // 1. Cek dulu, kalau kolom 'speed' BELUM ada, baru buat.
            // (Ini solusi agar tidak error "Duplicate column")
            if (!Schema::hasColumn('products', 'speed')) {
                $table->string('speed')->nullable(); 
            }

            // 2. Tambahkan kolom 'duration' (karena ini yang belum ada)
            if (!Schema::hasColumn('products', 'duration')) {
                $table->integer('duration')->default(30); 
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Hapus kolom saat rollback, tapi cek dulu biar aman
            if (Schema::hasColumn('products', 'speed')) {
                $table->dropColumn('speed');
            }
            if (Schema::hasColumn('products', 'duration')) {
                $table->dropColumn('duration');
            }
        });
    }
};