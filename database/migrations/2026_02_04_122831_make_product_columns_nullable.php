<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Kita ubah agar kolom-kolom ini BOLEH KOSONG (NULL)
            $table->integer('stock')->nullable()->change();
            $table->integer('duration')->nullable()->change();
            $table->string('speed')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Kembalikan ke tidak boleh kosong (opsional)
            $table->integer('stock')->nullable(false)->change();
            $table->integer('duration')->nullable(false)->change();
            $table->string('speed')->nullable(false)->change();
        });
    }
};
