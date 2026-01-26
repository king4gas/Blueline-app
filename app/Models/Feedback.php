<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'feedbacks'; 

    // Mengizinkan semua kolom diisi kecuali ID
    protected $guarded = ['id'];

    // === BAGIAN INI YANG WAJIB DITAMBAHKAN ===
    // Agar Feedback::with('user') bisa berjalan
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}