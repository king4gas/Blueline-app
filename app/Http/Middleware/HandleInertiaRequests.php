<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Report; // <--- Import Model Laporan
use App\Models\Message; // <--- Import Model Chat (Sesuaikan jika namanya beda)
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            
            // Hitung Keranjang (User)
            'cartCount' => Auth::check() 
                ? (int) Cart::where('user_id', Auth::id())->count() 
                : 0,

            // Hitung Pesanan Masuk (Admin)
            'incomingOrderCount' => Auth::check() && Auth::user()->role === 'admin' 
                ? (int) Order::whereNotIn('status', ['completed', 'finished', 'returned', 'canceled', 'rejected'])->count() 
                : 0,
            
            // === NOTIF PUSAT BANTUAN (ADMIN) ===
            'pendingReportCount' => Auth::check() && Auth::user()->role === 'admin' 
                ? (int) Report::where('status', 'pending')->count() 
                : 0,

            // === NOTIF LIVE CHAT (ADMIN) ===
           // === NOTIF LIVE CHAT (ADMIN) ===
            'unreadChatCount' => Auth::check() && Auth::user()->role === 'admin' 
                ? (int) \App\Models\Message::where('is_read', false)->where('is_admin', false)->count() 
                : 0,
            // ===============================

            'flash' => [
                'message' => fn () => $request->session()->get('message'),
            ],
        ]);
    }
}