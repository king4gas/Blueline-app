<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        // Menampilkan SEMUA produk untuk halaman katalog
        return Inertia::render('Products/Index', [
            'products' => Product::latest()->get()
        ]);
    }
}