<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * (Daftar kolom yang boleh diisi datanya)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'type',   // 'hardware' atau 'service'
        'stock',  // nullable (khusus hardware)
        'speed',  // nullable (khusus service)
        'image',
        'is_featured' // Kolom baru untuk menandai produk unggulan
    ];

    /**
     * The attributes that should be cast.
     * (Memastikan tipe data keluarannya benar)
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2', // Memastikan harga dianggap angka desimal
        'stock' => 'integer',
    ];

    /**
     * Get the route key for the model.
     * (Agar nanti URL-nya menggunakan slug, bukan ID)
     * Contoh: /product/paket-internet-gaming (Bukan /product/1)
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}