<?php

namespace Database\Seeders;

use App\Models\Product; // Pastikan model Product ada (Jika belum, buat: php artisan make:model Product)
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Contoh Produk Layanan Internet
        Product::create([
            'name' => 'Blueline Home 50Mbps',
            'slug' => 'blueline-home-50mbps',
            'description' => 'Internet fiber optik stabil, cocok untuk streaming 4K dan gaming.',
            'price' => 350000,
            'type' => 'service',
            'speed' => '50 Mbps',
            'image' => 'https://placehold.co/600x400/007bff/ffffff?text=Internet+50Mbps', // Placeholder image
        ]);

        Product::create([
            'name' => 'Blueline Gamer 100Mbps',
            'slug' => 'blueline-gamer-100mbps',
            'description' => 'Kecepatan ngebut tanpa lag, prioritas trafik gaming.',
            'price' => 650000,
            'type' => 'service',
            'speed' => '100 Mbps',
            'image' => 'https://placehold.co/600x400/6610f2/ffffff?text=Gamer+100Mbps',
        ]);

        // 2. Contoh Produk Hardware
        Product::create([
            'name' => 'Router TP-Link Archer C54',
            'slug' => 'router-tp-link-archer-c54',
            'description' => 'Dual band router AC1200, jangkauan luas.',
            'price' => 450000,
            'type' => 'hardware',
            'stock' => 20,
            'image' => 'https://placehold.co/600x400/28a745/ffffff?text=Router+Wifi',
        ]);
    }
}