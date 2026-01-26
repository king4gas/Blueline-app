<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Http\Request;

class OrderReturnController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'evidence' => 'required|image|max:2048', // Wajib upload foto bukti
        ]);

        // Cek apakah sudah pernah mengajukan untuk order ini
        if(OrderReturn::where('order_id', $order->id)->exists()) {
            return back()->withErrors(['error' => 'Anda sudah mengajukan pengembalian untuk pesanan ini.']);
        }

        $path = $request->file('evidence')->store('returns', 'public');

        OrderReturn::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'reason' => $request->reason,
            'evidence' => '/storage/' . $path,
            'status' => 'pending'
        ]);

        return back()->with('message', 'Pengajuan pengembalian berhasil dikirim. Tunggu konfirmasi Admin.');
    }
}