<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Cek dulu biar tidak error "Duplicate column"
            if (!Schema::hasColumn('complaints', 'status')) {
                // Status: pending (Baru), processing (Diproses), resolved (Selesai)
                $table->string('status')->default('pending'); 
            }
        });
    }

    public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            if (Schema::hasColumn('complaints', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};