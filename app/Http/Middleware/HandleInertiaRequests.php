<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Cart; // <--- Import Model Cart
use App\Models\Order; // <--- Import Model Order untuk notif admin
use Illuminate\Support\Facades\Auth; // <--- Import Auth

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            
            // Hitung Keranjang (User)
            'cartCount' => Auth::check() 
                ? (int) \App\Models\Cart::where('user_id', Auth::id())->count() 
                : 0,

            // === LOGIKA FINAL NOTIFIKASI ADMIN ===
            // Menghitung semua order, KECUALI yang statusnya sudah Selesai/Dibatalkan
            'incomingOrderCount' => Auth::check() && Auth::user()->role === 'admin' 
                ? (int) \App\Models\Order::whereNotIn('status', [
                    'completed', 
                    'finished', 
                    'returned', 
                    'canceled', 
                    'rejected'
                  ])->count() 
                : 0,
            // =====================================

            'flash' => [
                'message' => fn () => $request->session()->get('message'),
            ],
        ]);
    }
}