<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')->where('user_id', auth()->id())->get();
        
        // Hitung total harga semua item di keranjang (opsional, krn di frontend juga dihitung)
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

    // [BARU] Method Update Quantity
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

    public function checkout(Request $request)
    {
        $request->validate([
            'selected_cart_ids' => 'required|array|min:1',
            'address' => 'required|string',
            'phone' => 'required|string',
            'payment_proof' => 'required|image|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            
            // Ambil item yang DIPILIH (Checklist) saja
            $cartItems = Cart::with('product')
                ->where('user_id', auth()->id())
                ->whereIn('id', $request->selected_cart_ids)
                ->get();

            $totalPrice = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $totalPrice,
                'status' => 'pending_verification',
                'payment_proof' => $path,
                'shipping_address' => $request->address, // Pastikan kolom ini ada di DB orders
                // Jika tidak ada kolom shipping_address/phone, simpan di notes atau buat kolom baru
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            // Hapus item yang sudah di-checkout dari keranjang
            Cart::whereIn('id', $request->selected_cart_ids)->delete();
        });

        return redirect()->route('my-orders');
    }
}