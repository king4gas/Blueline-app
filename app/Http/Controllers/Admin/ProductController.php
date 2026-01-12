<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    // 1. Tampilkan Daftar Produk
    public function index()
    {
        return Inertia::render('Admin/Products/Index', [
            // Kita gunakan get() agar diterima sebagai Array di Vue
            'products' => Product::latest()->get()
        ]);
    }

    // 2. Form Tambah
    public function create()
    {
        return Inertia::render('Admin/Products/Create');
    }

    // 3. Simpan Produk
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'type' => 'required|in:hardware,service',
            'description' => 'required|string',
            'image' => 'required|image|max:2048',
            'stock' => 'nullable|integer',
            'speed' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        $imagePath = $request->file('image')->store('products', 'public');

        Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'price' => $request->price,
            'type' => $request->type,
            'description' => $request->description,
            'image' => '/storage/' . $imagePath,
            'stock' => $request->stock,
            'speed' => $request->speed,
            'is_featured' => $request->is_featured ?? false,
        ]);

        return redirect()->route('admin.products.index')->with('message', 'Produk berhasil ditambahkan!');
    }

    // 4. Hapus Produk (VERSI AMAN ANTI-WHITE SCREEN)
    public function destroy($id)
    {
        // Cari manual agar tidak 404 jika ID salah, tapi return error
        $product = Product::find($id);

        if (!$product) {
            return back()->withErrors(['error' => 'Produk tidak ditemukan.']);
        }

        try {
            // 1. Cek & Hapus Gambar
            if ($product->image) {
                $path = str_replace('/storage/', '', $product->image);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            // 2. Hapus Database
            $product->delete();

            return back()->with('message', 'Produk berhasil dihapus!');

        } catch (\Exception $e) {
            // TANGKAP SEMUA ERROR (Termasuk Foreign Key / Relasi)
            // Agar layar tidak putih, kita kembalikan user ke halaman sebelumnya dengan pesan
            return back()->withErrors(['error' => 'GAGAL: Produk tidak bisa dihapus karena ada di riwayat pesanan pelanggan.']);
        }
    }
}