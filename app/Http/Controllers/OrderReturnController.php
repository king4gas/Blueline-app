<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Jangan lupa import ini

class OrderReturnController extends Controller
{
    public function store(Request $request, Order $order)
    {
        // 1. Validasi Input User
        $request->validate([
            'reason' => 'required|string',
            'image' => 'required|image|max:5120', // Wajib Foto (Max 5MB)
            'video' => 'nullable|mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4|max:20480', // Opsional Video (Max 20MB)
        ]);

        // 2. Upload Foto
        $imagePath = $request->file('image')->store('returns/images', 'public');

        // 3. Upload Video (Jika ada)
        $videoPath = null;
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('returns/videos', 'public');
        }

        // 4. Simpan ke Database
        OrderReturn::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'reason' => $request->reason,
            'image' => '/storage/' . $imagePath,
            'video' => $videoPath ? '/storage/' . $videoPath : null,
            'status' => 'pending' // Status awal: Menunggu persetujuan Admin
        ]);

        // 5. Update Status Order (Opsional, visual saja)
        $order->update(['status' => 'return_requested']);

        return back()->with('message', 'Bukti berhasil diupload! Mohon tunggu verifikasi Admin.');
    }
}