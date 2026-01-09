<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat 1 Akun Admin
        User::factory()->create([
            'name' => 'Admin Blueline',
            'email' => 'admin@blueline.com',
            'password' => Hash::make('password'), // passwordnya 'password'
            'role' => 'admin',
        ]);

        // Buat 1 Akun User biasa untuk tes
        User::factory()->create([
            'name' => 'Pelanggan Setia',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}