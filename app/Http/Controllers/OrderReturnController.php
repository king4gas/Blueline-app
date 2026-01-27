<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderReturnController extends Controller
{
    public function store(Request $request, Order $order)
    {
        // 1. Validasi Input
        $request->validate([
            'reason' => 'required|string',
            'evidence' => 'required|image|max:2048' // Maksimal 2MB
        ]);

        // 2. Pastikan order milik user yang login (Keamanan)
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // 3. Upload Foto Bukti
        $path = null;
        if ($request->hasFile('evidence')) {
            $path = $request->file('evidence')->store('returns', 'public');
        }

        // 4. UPDATE DATABASE (PENTING!)
        // Kita harus mengubah status menjadi 'return_requested' agar Admin bisa melihatnya.
        $order->update([
            'status' => 'return_requested', // <--- INI KUNCINYA
            'return_reason' => $request->reason,
            'return_evidence' => $path,
        ]);

        // 5. Redirect kembali dengan pesan sukses
        return back()->with('message', 'Pengajuan pengembalian berhasil dikirim. Tunggu konfirmasi Admin.');
    }
}