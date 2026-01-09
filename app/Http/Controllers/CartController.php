<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CartController extends Controller
{
    // 1. Tampilkan Halaman Keranjang
    public function index()
    {
        $carts = Cart::with('product')->where('user_id', auth()->id())->get();
        return Inertia::render('Cart/Index', ['carts' => $carts]);
    }

    // 2. Tambah ke Keranjang
    public function store(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        // Cek apakah produk sudah ada di cart user ini
        $cart = Cart::where('user_id', auth()->id())
                    ->where('product_id', $product->id)
                    ->first();

        if ($cart) {
            $cart->increment('quantity');
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => 1
            ]);
        }

        return redirect()->back()->with('message', 'Masuk keranjang!');
    }

    // 3. Hapus Item Keranjang
    public function destroy($id)
    {
        Cart::where('id', $id)->where('user_id', auth()->id())->delete();
        return redirect()->back();
    }

    // 4. PROSES CHECKOUT (Payment Proof)
    public function checkout(Request $request)
    {
        $request->validate([
            'selected_cart_ids' => 'required|array', // ID cart yang dichecklist
            'address' => 'required|string',
            'phone' => 'required|string',
            'payment_proof' => 'required|image|max:2048', // Wajib upload gambar
        ]);

        DB::transaction(function () use ($request) {
            // Ambil item cart yang dipilih saja
            $cartItems = Cart::with('product')
                ->whereIn('id', $request->selected_cart_ids)
                ->where('user_id', auth()->id())
                ->get();

            if ($cartItems->isEmpty()) return;

            // Hitung Total
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += $item->product->price * $item->quantity;
            }

            // Upload Bukti Transfer
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');

            // Buat Order Header
            $order = Order::create([
                'user_id' => auth()->id(),
                'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
                'total_price' => $totalPrice,
                'status' => 'pending_verification', // Masuk admin panel nanti
                'payment_proof' => $path,
                'address' => $request->address,
                'phone' => $request->phone,
            ]);

            // Pindahkan Cart ke OrderItem
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                ]);

                // Kurangi stok jika hardware
                if($item->product->type === 'hardware') {
                     $item->product->decrement('stock', $item->quantity);
                }
            }

            // Hapus item dari keranjang setelah dibeli
            Cart::whereIn('id', $request->selected_cart_ids)->delete();
        });

        return redirect()->route('home')->with('message', 'Pesanan dikirim! Menunggu verifikasi admin.');
    }
}