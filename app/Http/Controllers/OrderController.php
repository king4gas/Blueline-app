<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        // 1. Ambil data Order + Item Produk + Status Retur
        $orders = Order::with(['items.product', 'returnRequest']) 
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        // 2. Render ke Tampilan Vue
        // PERHATIKAN BARIS INI: Sesuaikan dengan nama folder di resources/js/Pages/
        // Jika file Anda ada di folder "Order", pakai 'Order/MyOrders'
        return Inertia::render('Order/MyOrders', [ 
            'orders' => $orders
        ]);
    }
}