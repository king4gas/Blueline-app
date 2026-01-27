<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    // === 1. METHOD INDEX: MENAMPILKAN HALAMAN LAYANAN SAYA ===
    public function index()
    {
        // Cari Order Terakhir yang statusnya SUKSES (Verified/Shipped/Completed)
        // Dan mengandung produk tipe 'service' (Layanan Internet)
        $lastSubscriptionOrder = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['verified', 'shipped', 'completed'])
            ->whereHas('items.product', function($query) {
                $query->where('type', 'service');
            })
            ->latest() // Ambil yang paling baru
            ->first();

        $subscriptionData = null;

        if ($lastSubscriptionOrder) {
            // Ambil item produk layanannya
            $item = $lastSubscriptionOrder->items->first(function($i) {
                return $i->product && $i->product->type === 'service';
            });

            if ($item) {
                // Hitung tanggal expired (30 hari setelah order dibuat)
                $startDate = Carbon::parse($lastSubscriptionOrder->created_at);
                $expiredDate = $startDate->copy()->addDays(30);
                $now = Carbon::now();

                // Hitung sisa hari
                $daysRemaining = $now->diffInDays($expiredDate, false); 
                $progress = 0;
                
                // Logic Progress Bar (30 hari = 100%)
                if ($daysRemaining > 0) {
                    $daysPassed = 30 - $daysRemaining;
                    $progress = ($daysPassed / 30) * 100;
                } else {
                    $progress = 100; // Sudah expired
                }

                $subscriptionData = [
                    'product' => $item->product,
                    'start_date' => $startDate->translatedFormat('d F Y'),
                    'expired_date' => $expiredDate->translatedFormat('d F Y'),
                    'days_remaining' => (int) $daysRemaining,
                    'is_active' => $daysRemaining > 0,
                    'progress' => $progress,
                    'price' => $item->product->price
                ];
            }
        }

        return Inertia::render('Subscription/Index', [
            'subscription' => $subscriptionData
        ]);
    }

    // === 2. METHOD RENEW: FITUR PERPANJANG LANGSUNG (SKIP CART) ===
    public function renew(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $product = Product::findOrFail($request->product_id);

        return DB::transaction(function () use ($product) {
            // A. Generate Invoice Unik
            $invoiceNumber = 'INV-SUB-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // B. Buat Order Langsung (Status: Pending Payment)
            $order = Order::create([
                'user_id' => auth()->id(),
                'invoice_number' => $invoiceNumber,
                'total_price' => $product->price,
                'status' => 'pending_payment', 
                'payment_proof' => null, 
                'address' => 'Digital Subscription (Auto Renew)', 
                'phone' => auth()->user()->email, 
            ]);

            // C. Masukkan Item ke Order
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => 1,
                'price' => $product->price,
            ]);

            // D. Redirect langsung ke halaman Pembayaran / Upload Bukti
            return to_route('payment.show', $order->id);
        });
    }
}