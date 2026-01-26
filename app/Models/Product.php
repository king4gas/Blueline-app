<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'image',
        'type',
        'is_featured', 
    ];

    // === TAMBAHKAN KODE INI DI BAWAH ===
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}