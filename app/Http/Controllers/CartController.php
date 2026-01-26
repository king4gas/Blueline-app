<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem; // <--- Pastikan ini ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // <--- Pastikan ini ada
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')->where('user_id', auth()->id())->get();
        
        $total = $carts->sum(function($cart) {
            return $cart->product->price * $cart->quantity;
        });

        return Inertia::render('Cart/Index', [
            'carts' => $carts,
            'total' => $total
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = Cart::where('user_id', auth()->id())
                    ->where('product_id', $request->product_id)
                    ->first();

        if ($cart) {
            $cart->increment('quantity');
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => 1
            ]);
        }

        return redirect()->back()->with('message', 'Produk ditambahkan ke keranjang');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::where('user_id', auth()->id())->where('id', $id)->firstOrFail();
        $cart->update([
            'quantity' => $request->quantity
        ]);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $cart = Cart::where('user_id', auth()->id())->where('id', $id)->firstOrFail();
        $cart->delete();

        return redirect()->back();
    }

    // === CHECKOUT FINAL (SUDAH DIPERBAIKI) ===
    public function checkout(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'selected_cart_ids' => 'required|array|min:1',
            'address' => 'required|string',
            'phone' => 'required|string',
            'payment_proof' => 'required|image|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            // 2. Upload Gambar
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            
            // 3. Ambil Keranjang
            $cartItems = Cart::with('product')
                ->where('user_id', auth()->id())
                ->whereIn('id', $request->selected_cart_ids)
                ->get();

            // 4. Hitung Total
            $totalPrice = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
            
            // 5. Buat Nomor Invoice
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // 6. Simpan Order
            $order = Order::create([
                'user_id' => auth()->id(),
                'invoice_number' => $invoiceNumber,
                'total_price' => $totalPrice,
                'status' => 'pending_verification',
                'payment_proof' => $path,
                'address' => $request->address,
                'phone' => $request->phone,
            ]);

            // 7. Simpan Detail Item
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name, // <--- Perbaikan Terakhir
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            // 8. Hapus dari Keranjang
            Cart::whereIn('id', $request->selected_cart_ids)->delete();
        });

        return redirect()->route('my-orders');
    }
}