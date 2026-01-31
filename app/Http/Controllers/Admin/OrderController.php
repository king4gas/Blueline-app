<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    // TAMPILKAN DAFTAR ORDER
    public function index(Request $request)
    {
        // Load user untuk nama, returnRequest untuk cek status retur
        $query = Order::with(['user', 'returnRequest', 'items']); 

        // Filter Status (Tab Menu)
        if ($request->has('status')) {
            if ($request->status === 'return_requested') {
                // Khusus tab retur, cari yang punya request retur
                $query->whereHas('returnRequest'); 
            } else {
                // Tab status biasa
                $query->where('status', $request->status);
            }
        }

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $query->latest()->get(),
            'filterStatus' => $request->status // Kirim balik untuk active tab state
        ]);
    }

    // UPDATE STATUS ORDER BIASA
    public function update(Request $request, Order $order)
    {
        $request->validate(['status' => 'required']);
        $order->update(['status' => $request->status]);
        
        return back()->with('message', 'Status pesanan berhasil diperbarui.');
    }

    // VERIFIKASI RETUR (Logic Flowchart)
    public function verifyReturn(Request $request, Order $order)
    {
        // Validasi input dari Vue (Modal Admin)
        $request->validate([
            'status' => 'required|in:approved,rejected,item_received,completed',
            'admin_note' => 'nullable|string'
        ]);

        // 1. Update Status di Tabel OrderReturn (Tabel Anak)
        // Kita pakai updateOrCreate jaga-jaga, tapi update() saja cukup jika data pasti ada
        $order->returnRequest()->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note
        ]);

        // 2. Sinkronisasi Status Order Utama (Tabel Induk)
        // Ini opsional tapi bagus untuk UX agar list order utama statusnya jelas
        if ($request->status === 'completed') {
            $order->update(['status' => 'returned']); // Order selesai, barang kembali
        } 
        elseif ($request->status === 'rejected') {
            // Jika ditolak, kembalikan status order jadi completed (selesai normal)
            // agar user tidak bisa retur lagi (kecuali logic tombol diubah)
            $order->update(['status' => 'completed']); 
        }

        return back()->with('message', 'Keputusan retur berhasil disimpan.');
    }
}