<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    // 1. Tampilkan Semua Pesanan
    public function index()
    {
        $orders = Order::with(['user', 'items']) // Ambil data user & item sekaligus
            ->latest()
            ->paginate(10); // 10 per halaman

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders
        ]);
    }

    // 2. Update Status Pesanan (Verifikasi/Kirim/Tolak)
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending_verification,verified,shipped,completed,rejected'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return back()->with('message', 'Status pesanan berhasil diperbarui!');
    }
}