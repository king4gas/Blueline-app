<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Report; 
use App\Models\Message; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Wajib di-import untuk logika waktu masa aktif

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        // === LOGIKA CEK STATUS LANGGANAN AKTIF ===
        $isSubscribed = false;

        if (Auth::check()) {
            // Cari order terakhir khusus produk 'service' yang sukses
            $lastSubscriptionOrder = Order::with('items.product')
                ->where('user_id', Auth::id())
                ->whereIn('status', ['verified', 'shipped', 'completed', 'finished'])
                ->whereHas('items.product', function($query) {
                    $query->where('type', 'service');
                })
                ->latest()
                ->first();

            if ($lastSubscriptionOrder) {
                $item = $lastSubscriptionOrder->items->first(function($i) {
                    return $i->product && $i->product->type === 'service';
                });

                if ($item) {
                    // Hitung masa aktif berdasarkan durasi produk
                    $duration = $item->product->duration ?? 30; 
                    $expiredDate = Carbon::parse($lastSubscriptionOrder->created_at)->addDays($duration);
                    $now = Carbon::now('Asia/Makassar');

                    // Jika waktu sekarang masih di bawah tanggal expired, berarti LAYANAN AKTIF (true)
                    $isSubscribed = $now->lessThan($expiredDate);
                }
            }
        }
        // ===========================================

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
                // Data ini dikirim ke Vue sebagai global props
                'is_subscribed' => $isSubscribed, 
            ],
            
            // Hitung Keranjang (User)
            'cartCount' => Auth::check() 
                ? (int) Cart::where('user_id', Auth::id())->count() 
                : 0,

            // Hitung Pesanan Masuk (Admin)
            'incomingOrderCount' => Auth::check() && Auth::user()->role === 'admin' 
                ? (int) Order::whereNotIn('status', ['completed', 'finished', 'returned', 'canceled', 'rejected'])->count() 
                : 0,
            
            // Notif Pusat Bantuan (Admin)
            'pendingReportCount' => Auth::check() && Auth::user()->role === 'admin' 
                ? (int) Report::where('status', 'pending')->count() 
                : 0,

            // Notif Live Chat (Admin)
            'unreadChatCount' => Auth::check() && Auth::user()->role === 'admin' 
                ? (int) Message::where('is_read', false)->where('is_admin', false)->count() 
                : 0,

            'flash' => [
                'message' => fn () => $request->session()->get('message'),
            ],
        ]);
    }
}