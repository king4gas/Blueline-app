<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    // 1. Tampilkan Daftar Pesanan User
    public function index()
    {
        // Ambil data Order + Item Produk + Status Retur
        $orders = Order::with(['items.product', 'returnRequest']) 
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Order/MyOrders', [ 
            'orders' => $orders
        ]);
    }

    // 2. SIMULATOR PEMBAYARAN OTOMATIS (KHUSUS DEMO SKRIPSI)
    public function simulatePayment(Order $order)
    {
        // Keamanan: Pastikan hanya pemilik order yang bisa klik tombol ini
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Akses Ditolak.');
        }

        // Langsung ubah status jadi 'verified' (seolah-olah mutasi uang terdeteksi)
        $order->update([
            'status' => 'verified',
            'payment_proof' => 'VERIFIED_BY_SYSTEM' // Teks dummy agar kolom bukti tidak null
        ]);

        return back()->with('message', 'Simulasi Berhasil! Dana otomatis terdeteksi masuk.');
    }

    // 3. MENYIMPAN PENGAJUAN RETUR DARI USER
    public function submitReturn(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Validasi input
        $request->validate([
            'reason' => 'required|string',
            'image' => 'required|image|max:2048', 
            'video' => 'nullable|mimes:mp4,mov,ogg,qt|max:20480', 
        ]);

        // Upload File
        $imagePath = '/storage/' . $request->file('image')->store('returns', 'public');
        $videoPath = $request->hasFile('video') ? '/storage/' . $request->file('video')->store('returns_video', 'public') : null;

        // Simpan Request Retur ke Database
        $order->returnRequest()->create([
            'reason' => $request->reason,
            'image' => $imagePath,
            'video' => $videoPath,
            'status' => 'pending' // Status awal menuggu admin
        ]);

        // Opsional: Ubah status order utama agar seragam
        $order->update(['status' => 'return_requested']);

        return back()->with('message', 'Pengajuan retur berhasil dikirim. Menunggu admin.');
    }
    public function completeByUser(\App\Models\Order $order)
    {
        // Cek agar user tidak bisa menyelesaikan pesanan orang lain
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->update([
            'status' => 'finished' // Atau 'completed' jika struktur DB Anda begitu
        ]);

        return redirect()->back();
    }
}