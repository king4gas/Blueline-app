<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    // Menampilkan Halaman Tagihan / Invoice
    public function show(Order $order)
    {
        // Pastikan order milik user yang login
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Load relasi produk untuk ditampilkan detailnya
        $order->load('items.product');

        return Inertia::render('Payment/Show', [
            'order' => $order
        ]);
    }

    // Proses Upload Bukti Transfer
    public function upload(Request $request, Order $order)
    {
        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ]);

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'status' => 'pending_verification' // Update status jadi menunggu verifikasi admin
        ]);

        return redirect()->route('subscription.index')->with('message', 'Pembayaran berhasil dikirim! Menunggu konfirmasi admin.');
    }
}