<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Untuk buat slug otomatis
use Inertia\Inertia;

class ProductController extends Controller
{
    // 1. Tampilkan Daftar Produk
    public function index()
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => Product::latest()->paginate(10)
        ]);
    }

    // 2. Tampilkan Form Tambah Produk
    public function create()
    {
        return Inertia::render('Admin/Products/Create');
    }

    // 3. Proses Simpan Produk (Upload Gambar)
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'type' => 'required|in:hardware,service',
            'description' => 'required|string',
            'image' => 'required|image|max:2048', // Maks 2MB
            // Stok wajib jika hardware, Speed wajib jika service
            'stock' => 'required_if:type,hardware|nullable|integer',
            'speed' => 'required_if:type,service|nullable|string',
        ]);

        // Upload Gambar ke folder public/storage/products
        $imagePath = $request->file('image')->store('products', 'public');

        // Simpan ke Database
        Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5), // Slug unik
            'price' => $request->price,
            'type' => $request->type,
            'description' => $request->description,
            'image' => '/storage/' . $imagePath, // Simpan path lengkap agar mudah dipanggil
            'stock' => $request->stock,
            'speed' => $request->speed,
        ]);

        return redirect()->route('admin.products.index')->with('message', 'Produk berhasil ditambahkan!');
    }

    // 4. Hapus Produk
    public function destroy(Product $product)
    {
        // Hapus gambar lama agar hemat memori
        if ($product->image) {
            $path = str_replace('/storage/', '', $product->image);
            Storage::disk('public')->delete($path);
        }

        $product->delete();
        return back()->with('message', 'Produk dihapus!');
    }
}