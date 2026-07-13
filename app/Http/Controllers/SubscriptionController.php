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
// Tambahan untuk fitur Email:
use Illuminate\Support\Facades\Mail;
use App\Mail\NewOrderNotification;

class SubscriptionController extends Controller
{
    // === 1. METHOD INDEX: MENAMPILKAN HALAMAN LAYANAN SAYA ===
    public function index()
    {
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
                $duration = $item->product->duration ?? 30; 
                
                $startDate = Carbon::parse($lastSubscriptionOrder->created_at);
                $expiredDate = $startDate->copy()->addDays($duration);
                $now = Carbon::now('Asia/Makassar');

                $penaltyDate = $expiredDate->copy()->addMonth()->startOfMonth();
                $terminationDate = $expiredDate->copy()->addMonths(2);
                $daysRemaining = $now->diffInDays($expiredDate, false);
                
                $penaltyFee = 0;
                $isTerminated = false;
                $isActive = true;

                if ($now->greaterThanOrEqualTo($terminationDate)) {
                    $isTerminated = true;
                    $isActive = false;
                    $penaltyFee = $item->product->price * 0.05; 
                } elseif ($now->greaterThanOrEqualTo($penaltyDate)) {
                    $isActive = false;
                    $penaltyFee = $item->product->price * 0.05; 
                } elseif ($daysRemaining <= 0) {
                    $isActive = false; 
                }

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
            'expected_total' => 'nullable|numeric' 
        ]);

        $product = Product::findOrFail($request->product_id);

        return DB::transaction(function () use ($product, $request) {
            
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
                    $penaltyFee = $product->price * 0.05; 
                }
            }

            $totalPrice = $product->price + $penaltyFee;

            $invoiceNumber = 'INV-SUB-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $order = Order::create([
                'user_id' => auth()->id(),
                'invoice_number' => $invoiceNumber,
                'total_price' => $totalPrice, 
                'status' => 'pending_payment', 
                'payment_proof' => null, 
                'address' => 'Digital Subscription (Auto Renew)', 
                'phone' => auth()->user()->phone ?? '000', 
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name, 
                'quantity' => 1,
                'price' => $product->price,
            ]);

            if ($penaltyFee > 0) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id, 
                    'product_name' => 'Denda Keterlambatan (5%) - ' . $product->name, 
                    'quantity' => 1,
                    'price' => $penaltyFee,
                ]);
            }

            // MENGIRIM EMAIL NOTIFIKASI KE ADMIN
            Mail::to('anakagungekaw11@gmail.com')->send(new NewOrderNotification($order));

            return to_route('my-orders')->with('message', 'Tagihan berhasil dibuat. Silakan lakukan pembayaran.');
        });
    }
}