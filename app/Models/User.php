<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Uncomment jika pakai verifikasi email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin', // PENTING: Agar bisa diisi via Seeder/Coding
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean', // Memastikan nilainya selalu True/False
    ];

    // === RELASI DATABASE LENGKAP ===

    // 1. User punya banyak Pesanan (Order)
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // 2. User punya banyak Feedback (Testimoni)
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    // 3. User punya banyak Komplain (Fitur Lama)
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    // 4. User punya banyak Pesan Chat (Fitur Chat Baru)
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}