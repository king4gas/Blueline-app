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
        // Cari Order Terakhir yang statusnya SUKSES (Verified/Shipped/Completed/Finished)
        $lastSubscriptionOrder = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['verified', 'shipped', 'completed', 'finished'])
            ->whereHas('items.product', function($query) {
                $query->where('type', 'service');
            })
            ->latest()
            ->first();

        $subscriptionData = null;

        if ($lastSubscriptionOrder) {
            $item = $lastSubscriptionOrder->items->first(function($i) {
                return $i->product && $i->product->type === 'service';
            });

            if ($item) {
                // Asumsi durasi paket standar = 30 Hari
                $duration = $item->product->duration ?? 30; 
                
                $startDate = Carbon::parse($lastSubscriptionOrder->created_at);
                $expiredDate = $startDate->copy()->addDays($duration);
                $now = Carbon::now('Asia/Makassar');

                // --- LOGIKA DENDA & PUTUS ---
                // Denda 5%: Dimulai pada tanggal 1 di bulan berikutnya setelah masa aktif habis
                $penaltyDate = $expiredDate->copy()->addMonth()->startOfMonth();
                
                // Putus/Isolasi: 2 Bulan setelah masa expired
                $terminationDate = $expiredDate->copy()->addMonths(2);

                $daysRemaining = $now->diffInDays($expiredDate, false); // Nilai minus jika nunggak
                
                $penaltyFee = 0;
                $isTerminated = false;
                $isActive = true;

                // Cek status keterlambatan
                if ($now->greaterThanOrEqualTo($terminationDate)) {
                    $isTerminated = true;
                    $isActive = false;
                    $penaltyFee = $item->product->price * 0.05; // Denda 5%
                } elseif ($now->greaterThanOrEqualTo($penaltyDate)) {
                    $isActive = false;
                    $penaltyFee = $item->product->price * 0.05; // Denda 5%
                } elseif ($daysRemaining <= 0) {
                    $isActive = false; // Masa tenggang sebelum denda
                }

                // Progress Bar Logic (Dibalik agar sesuai animasi Vue: 0% = penuh, 100% = habis)
                $progress = 100;
                if ($daysRemaining > 0) {
                    $daysPassed = $duration - $daysRemaining;
                    $progress = ($daysPassed / $duration) * 100;
                }

                $subscriptionData = [
                    'product' => $item->product,
                    'start_date' => $startDate->translatedFormat('d F Y'),
                    'expired_date' => $expiredDate->translatedFormat('d F Y'),
                    'days_remaining' => (int) $daysRemaining,
                    'is_active' => $isActive,
                    'is_terminated' => $isTerminated,
                    'penalty_fee' => $penaltyFee,
                    'progress' => min(max($progress, 0), 100),
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
            'product_id' => 'required|exists:products,id',
            'expected_total' => 'nullable|numeric' // Menangkap ekspektasi harga dari frontend
        ]);

        $product = Product::findOrFail($request->product_id);

        return DB::transaction(function () use ($product, $request) {
            
            // CEK ULANG DENDA DI BACKEND (Mencegah manipulasi di Frontend)
            $lastSubscriptionOrder = Order::with('items')
                ->where('user_id', auth()->id())
                ->whereIn('status', ['verified', 'shipped', 'completed', 'finished'])
                ->whereHas('items.product', function($query) {
                    $query->where('type', 'service');
                })
                ->latest()
                ->first();

            $penaltyFee = 0;
            if ($lastSubscriptionOrder) {
                $duration = $product->duration ?? 30;
                $expiredDate = Carbon::parse($lastSubscriptionOrder->created_at)->addDays($duration);
                $penaltyDate = $expiredDate->copy()->addMonth()->startOfMonth();
                $now = Carbon::now('Asia/Makassar');

                if ($now->greaterThanOrEqualTo($penaltyDate)) {
                    $penaltyFee = $product->price * 0.05; // Hitung ulang denda 5%
                }
            }

            $totalPrice = $product->price + $penaltyFee;

            // Generate Invoice Unik
            $invoiceNumber = 'INV-SUB-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // Buat Order Langsung (Status: pending_payment)
            $order = Order::create([
                'user_id' => auth()->id(),
                'invoice_number' => $invoiceNumber,
                'total_price' => $totalPrice, // Harga total sudah termasuk denda jika ada
                'status' => 'pending_payment', 
                'payment_proof' => null, 
                'address' => 'Digital Subscription (Auto Renew)', 
                'phone' => auth()->user()->phone ?? '000', 
            ]);

            // Masukkan Item ke Order (Layanan Pokok)
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name, // Nama produk diisi
                'quantity' => 1,
                'price' => $product->price,
            ]);

            // Masukkan Item Denda (Jika Ada)
            if ($penaltyFee > 0) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id, // Menggunakan ID produk yang sama agar tidak error
                    'product_name' => 'Denda Keterlambatan (5%) - ' . $product->name, // Nama disesuaikan
                    'quantity' => 1,
                    'price' => $penaltyFee,
                ]);
            }

            // Arahkan ke Halaman Pesanan
            return to_route('my-orders')->with('message', 'Tagihan berhasil dibuat. Silakan lakukan pembayaran.');
        });
    }
}