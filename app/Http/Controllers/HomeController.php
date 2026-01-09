<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Welcome', [
            // Mengambil semua produk
            'featuredProducts' => Product::latest()->take(4)->get(),
            // Mengirim status login user ke frontend
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    }
}