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
        'stock',     // <--- WAJIB ADA DI SINI
        'speed',     
        'duration',  
        'image',
        'type',
        'is_featured', 
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}