<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    // Fungsi untuk memproses Checkout dari halaman Keranjang (Cart)
    public function process(Request $request)
    {
        // 1. Validasi Input (Hanya Alamat, HP, dan Item Keranjang. TANPA GAMBAR!)
        $request->validate([
            'selected_cart_ids' => 'required|array|min:1',
            'selected_cart_ids.*' => 'exists:carts,id',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
        ]);

        // 2. Ambil data item keranjang yang dicentang user
        $carts = Cart::with('product')
            ->whereIn('id', $request->selected_cart_ids)
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return back()->withErrors(['cart' => 'Keranjang kosong atau item tidak valid.']);
        }

        // 3. Hitung Total Harga Asli & Cek Ketersediaan Stok
        $subTotal = 0;
        foreach ($carts as $cart) {
            // Jika Hardware dan stok kurang dari yang mau dibeli, tolak!
            if ($cart->product->type === 'hardware' && $cart->product->stock < $cart->quantity) {
                return back()->withErrors(['stock' => 'Maaf, stok ' . $cart->product->name . ' tidak mencukupi.']);
            }
            $subTotal += ($cart->product->price * $cart->quantity);
        }

        // 4. Generate 3 Digit Kode Unik Skripsi
        $uniqueCode = rand(111, 999);
        $totalPay = $subTotal + $uniqueCode; // Total Tagihan = Subtotal + Kode Unik

        // 5. Simpan Pesanan dengan Database Transaction (Biar Aman)
        DB::transaction(function () use ($request, $carts, $uniqueCode, $totalPay) {
            
            // A. Buat record Order utama
            $order = Order::create([
                'user_id' => auth()->id(),
                'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
                'total_price' => $totalPay,   // Harga + Kode Unik
                'unique_code' => $uniqueCode, // Simpan kode uniknya
                'status' => 'pending_payment',// Menunggu Pembayaran
                'address' => $request->address,
                'phone' => $request->phone,
                'payment_proof' => null,      // KOSONGKAN BUKTI TRANSFER
            ]);

            // B. Pindahkan item dari Keranjang (Cart) ke Detail Pesanan (OrderItems)
            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->product->price,
                ]);

                // Kurangi stok database jika itu produk fisik/hardware
                if ($cart->product->type === 'hardware') {
                    $cart->product->decrement('stock', $cart->quantity);
                }

                // C. Hapus item tersebut dari keranjang user karena sudah jadi pesanan
                $cart->delete();
            }
        });

        // 6. Alihkan user ke halaman Riwayat Pesanan
        return redirect()->route('orders.index')
            ->with('message', 'Pesanan berhasil dibuat! Silakan lanjutkan pembayaran sesuai instruksi.');
    }
}