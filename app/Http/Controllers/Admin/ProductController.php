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
    public function index()
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => Product::latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'type' => 'required|in:hardware,service',
            'description' => 'required|string',
            'image' => 'required|image|max:2048',
            'stock' => 'required_if:type,hardware|nullable|integer',
            'speed' => 'required_if:type,service|nullable|string',
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

    // === EDIT FORM ===
    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product
        ]);
    }

    // === UPDATE PROCESS ===
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'type' => 'required|in:hardware,service',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048', // Boleh kosong saat edit
            'stock' => 'required_if:type,hardware|nullable|integer',
            'speed' => 'required_if:type,service|nullable|string',
            'is_featured' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'price' => $request->price,
            'type' => $request->type,
            'description' => $request->description,
            'stock' => $request->stock,
            'speed' => $request->speed,
            'is_featured' => $request->is_featured ?? false,
        ];

        // Cek jika ada gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($product->image) {
                $oldPath = str_replace('/storage/', '', $product->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            // Upload baru
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = '/storage/' . $imagePath;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('message', 'Produk berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) return back()->withErrors(['error' => 'Produk tidak ditemukan.']);

        try {
            if ($product->image) {
                $path = str_replace('/storage/', '', $product->image);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            $product->delete();
            return back()->with('message', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'GAGAL: Produk tidak bisa dihapus karena ada di riwayat pesanan.']);
        }
    }
}