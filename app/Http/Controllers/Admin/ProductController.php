<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    // === MENAMPILKAN DAFTAR PRODUK (DIFILTER) ===
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter berdasarkan tipe (hardware / service) jika ada di URL
        // Contoh: /admin/products?type=service
        if ($request->has('type') && $request->type !== null) {
            $query->where('type', $request->type);
        }

        $products = $query->latest()->get();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            // Kirim tipe ke frontend agar judul halaman bisa berubah
            // Misal: "Daftar Produk Fisik" atau "Daftar Layanan Internet"
            'filterType' => $request->type 
        ]);
    }

    // === FORM TAMBAH (CREATE) ===
    public function create(Request $request)
    {
        return Inertia::render('Admin/Products/Create', [
            // Kirim tipe default agar form tahu harus menampilkan input "Speed" atau tidak
            'type' => $request->type 
        ]);
    }

    // === SIMPAN DATA (STORE) ===
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:hardware,service', // Validasi tipe
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'image' => 'required|image|max:2048',
            
            // Validasi khusus jika tipe = service
            'speed' => 'nullable|string', // Contoh: "100 Mbps"
            'duration' => 'nullable|integer', // Contoh: 30 Hari
        ]);

        // Upload Gambar
        $imagePath = $request->file('image')->store('products', 'public');

        Product::create([
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
            'image' => '/storage/' . $imagePath,
            'speed' => $request->type === 'service' ? $request->speed : null,
            'duration' => $request->type === 'service' ? ($request->duration ?? 30) : null,
        ]);

        // Redirect kembali ke halaman index sesuai tipenya
        return redirect()->route('admin.products.index', ['type' => $request->type])
                         ->with('message', 'Data berhasil ditambahkan!');
    }

    // === FORM EDIT ===
    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product
        ]);
    }

    // === UPDATE DATA ===
    public function update(Request $request, Product $product)
    {
        // Gunakan Post tapi method spoofing di Laravel biasanya (atau form data)
        // Validasi
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:hardware,service',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'required|string',
            'speed' => 'nullable|string',
            'duration' => 'nullable|integer',
        ];

        // Gambar nullable saat update
        if ($request->hasFile('image')) {
            $rules['image'] = 'image|max:2048';
        }

        $request->validate($rules);

        $data = $request->only(['name', 'type', 'price', 'stock', 'description', 'speed', 'duration']);

        // Cek jika ada gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($product->image) {
                $oldPath = str_replace('/storage/', '', $product->image);
                Storage::disk('public')->delete($oldPath);
            }
            // Simpan gambar baru
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = '/storage/' . $path;
        }

        $product->update($data);

        return redirect()->route('admin.products.index', ['type' => $request->type])
                         ->with('message', 'Data berhasil diperbarui!');
    }

    // === HAPUS DATA ===
    public function destroy(Product $product)
    {
        $type = $product->type; // Simpan tipe sebelum dihapus untuk redirect

        if ($product->image) {
            $oldPath = str_replace('/storage/', '', $product->image);
            Storage::disk('public')->delete($oldPath);
        }

        $product->delete();

        return redirect()->route('admin.products.index', ['type' => $type])
                         ->with('message', 'Produk berhasil dihapus');
    }
}