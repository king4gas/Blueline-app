<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Cart; // <--- Import Model Cart
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
            // === LOGIKA HITUNG KERANJANG ===
            'cartCount' => Auth::check() 
                ? (int) Cart::where('user_id', Auth::id())->sum('quantity') 
                : 0,
            // ===============================
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
            ],
        ]);
    }
}