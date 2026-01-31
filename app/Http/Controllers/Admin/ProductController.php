<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        return Inertia::render('Admin/Products/Index', [
            'products' => $query->latest()->get(),
            'filters' => $request->only(['type'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Create');
    }

    // === PERBAIKAN DI SINI (STORE) ===
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'type' => 'required|in:service,hardware',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'stock' => 'nullable|integer', // <--- 1. Validasi Stok ditambahkan
            'speed' => 'nullable|string',
            'duration' => 'nullable|integer'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = '/storage/' . $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'type' => $request->type,
            'price' => $request->price,
            'stock' => $request->stock, // <--- 2. Data Stok DISIMPAN ke Database
            'description' => $request->description,
            'image' => $imagePath,
            'speed' => $request->speed,
            'duration' => $request->duration
        ]);

        return redirect()->route('admin.products.index', ['type' => $request->type])
            ->with('message', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product
        ]);
    }

    // === PERBAIKAN DI SINI (UPDATE) ===
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'type' => 'required|in:service,hardware',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'stock' => 'nullable|integer', // <--- 3. Validasi Stok ditambahkan
            'speed' => 'nullable|string',
            'duration' => 'nullable|integer'
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(5),
            'type' => $request->type,
            'price' => $request->price,
            'stock' => $request->stock, // <--- 4. Data Stok DIUPDATE ke Database
            'description' => $request->description,
            'speed' => $request->speed,
            'duration' => $request->duration
        ];

        if ($request->hasFile('image')) {
            if ($product->image) {
                $oldPath = str_replace('/storage/', '', $product->image);
                Storage::disk('public')->delete($oldPath);
            }
            $data['image'] = '/storage/' . $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index', ['type' => $request->type])
            ->with('message', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            $oldPath = str_replace('/storage/', '', $product->image);
            Storage::disk('public')->delete($oldPath);
        }

        $product->delete();

        return back()->with('message', 'Produk berhasil dihapus.');
    }
}