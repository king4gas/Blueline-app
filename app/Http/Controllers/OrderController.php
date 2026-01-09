<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        // Ambil pesanan punya user yang login, urutkan dari yang terbaru
        // Sertakan juga data 'items' agar kita bisa lihat detail barangnya
        $orders = Order::with('items')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Order/MyOrders', [
            'orders' => $orders
        ]);
    }
}