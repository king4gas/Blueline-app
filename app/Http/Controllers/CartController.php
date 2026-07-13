<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewOrderNotification;

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

    // === CHECKOUT FINAL (SUDAH DIPERBAIKI UNTUK SISTEM KODE UNIK & EMAIL) ===
    public function checkout(Request $request)
    {
        // 1. Validasi Input (HAPUS VALIDASI payment_proof)
        $request->validate([
            'selected_cart_ids' => 'required|array|min:1',
            'address' => 'required|string',
            'phone' => 'required|string',
        ]);

        // 2. Ambil Keranjang
        $cartItems = Cart::with('product')
            ->where('user_id', auth()->id())
            ->whereIn('id', $request->selected_cart_ids)
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->withErrors(['cart' => 'Keranjang kosong atau item tidak valid.']);
        }

        // 3. Hitung Total Subtotal + Generate Kode Unik
        $subTotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        $uniqueCode = rand(111, 999);
        $totalPrice = $subTotal + $uniqueCode; // Total Tagihan (Subtotal + Kode Unik)

        // 4. Buat Nomor Invoice
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        DB::transaction(function () use ($request, $cartItems, $totalPrice, $invoiceNumber, $uniqueCode) {
            
            // 5. Simpan Order
            $order = Order::create([
                'user_id' => auth()->id(),
                'invoice_number' => $invoiceNumber,
                'total_price' => $totalPrice, // Harga sudah termasuk kode unik
                'unique_code' => $uniqueCode, // Simpan kode unik
                'status' => 'pending_payment', // UBAH: Jadi pending_payment, bukan pending_verification
                'payment_proof' => null, // KOSONGKAN BUKTI TRANSFER
                'address' => $request->address,
                'phone' => $request->phone,
            ]);

            // 6. Simpan Detail Item
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name, // Perbaikan Terakhir Anda
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                // Kurangi stok database jika itu produk fisik/hardware
                if ($item->product->type === 'hardware') {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            // 7. Hapus dari Keranjang
            Cart::whereIn('id', $request->selected_cart_ids)->delete();

            // 8. KIRIM EMAIL NOTIFIKASI KE ADMIN
            Mail::to('anakagungekaw11@gmail.com')->send(new NewOrderNotification($order));
        });

        // Redirect ke halaman Riwayat Pesanan
        return redirect()->route('my-orders');
    }
}