<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'total_price',
        'status',
        'payment_proof',
        'address', 
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // === TAMBAHAN BARU ===
    // Relasi ini wajib ada agar fitur retur berjalan
    public function returnRequest()
    {
        return $this->hasOne(OrderReturn::class);
    }
}