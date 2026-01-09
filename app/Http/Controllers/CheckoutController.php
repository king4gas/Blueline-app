<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // 1. Tampilkan Halaman Form Checkout
    public function show(Product $product)
    {
        return Inertia::render('Checkout/Form', [
            'product' => $product,
            'user' => auth()->user() // Auto-fill data user
        ]);
    }

    // 2. Proses Simpan Pesanan (Store)
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'note' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Cek Stok (Khusus Hardware)
        if ($product->type === 'hardware' && $product->stock <= 0) {
            return back()->withErrors(['stock' => 'Maaf, stok barang ini habis.']);
        }

        // Gunakan Database Transaction agar aman
        DB::transaction(function () use ($request, $product) {
            // A. Buat Pesanan
            Order::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
                'total_price' => $product->price,
                'status' => 'pending',
                'address' => $request->address,
                'phone' => $request->phone,
                'note' => $request->note,
            ]);

            // B. Kurangi Stok (Jika Hardware)
            if ($product->type === 'hardware') {
                $product->decrement('stock');
            }
        });

        // Redirect ke halaman sukses (atau Riwayat Pesanan nanti)
        return redirect()->route('home')->with('message', 'Pesanan berhasil dibuat! Admin akan menghubungi Anda.');
    }
}