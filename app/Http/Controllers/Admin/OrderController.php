<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'items.product')->latest();

        // Filter Tab (Pending, Shipping, Completed, Returned)
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'return_requested') {
                $query->where('status', 'return_requested');
            } else {
                $query->where('status', $request->status);
            }
        }

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $query->get(),
            'filterStatus' => $request->status ?? 'all'
        ]);
    }

    // Update Status Biasa (Kirim Barang, Selesai, dll)
    public function update(Request $request, Order $order)
    {
        $request->validate(['status' => 'required']);
        $order->update(['status' => $request->status]);
        return back()->with('message', 'Status pesanan diperbarui');
    }

    // === FITUR BARU: ACC / TOLAK RETUR ===
    public function verifyReturn(Request $request, Order $order)
    {
        $request->validate([
            'decision' => 'required|in:approve,reject',
            'rejection_reason' => 'nullable|string'
        ]);

        if ($request->decision === 'approve') {
            // Jika diterima, status jadi 'returned'
            $order->update([
                'status' => 'returned'
            ]);
            // OPSI: Di sini bisa tambahkan logic kembalikan stok produk jika perlu
        } else {
            // Jika ditolak, status kembali 'completed' atau 'return_rejected'
            $order->update([
                'status' => 'return_rejected',
                'rejection_reason' => $request->rejection_reason
            ]);
        }

        return back()->with('message', 'Keputusan retur berhasil disimpan.');
    }
}