<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Ambil semua pesan
    public function index()
    {
        return Message::where('user_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();
    }

    // User kirim pesan
    public function store(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        // Simpan ke Database
        $msg = Message::create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_admin' => false, // False karena User yang kirim
            'is_read' => false
        ]);

        // Return JSON sukses agar frontend tahu data masuk
        return response()->json(['status' => 'success', 'data' => $msg], 201);
    }
}