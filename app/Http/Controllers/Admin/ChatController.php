<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cari User yang PUNYA pesan (WhereHas)
        // 2. Load pesan terakhir mereka (untuk preview di sidebar)
        // 3. Hitung pesan yang belum dibaca (unread_count)
        $users = User::whereHas('messages')
            ->with(['messages' => function($q) {
                $q->latest()->limit(1); 
            }])
            ->withCount(['messages as unread_count' => function($q) {
                $q->where('is_admin', false)->where('is_read', false);
            }])
            ->get()
            ->sortByDesc(function($user) {
                // Urutkan user berdasarkan waktu pesan terakhir
                return $user->messages->first()->created_at ?? 0;
            })
            ->values(); // Reset array keys agar rapi di JSON

        // LOGIC BUKA CHAT ROOM
        $selectedMessages = [];
        $activeUser = null;

        if ($request->has('user_id')) {
            $activeUser = User::find($request->user_id);
            
            if ($activeUser) {
                // Tandai chat user ini sebagai "Sudah Dibaca" oleh Admin
                Message::where('user_id', $activeUser->id)
                    ->where('is_admin', false)
                    ->update(['is_read' => true]);

                // Ambil semua chat user ini
                $selectedMessages = Message::where('user_id', $activeUser->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return Inertia::render('Admin/Chat/Index', [
            'users' => $users,
            'activeUser' => $activeUser,
            'messages' => $selectedMessages
        ]);
    }

    // Admin Balas Pesan
    public function store(Request $request, User $user)
    {
        $request->validate(['message' => 'required|string']);

        Message::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'is_admin' => true, // True karena Admin yang kirim
            'is_read' => false 
        ]);

        return back(); // Kalau admin boleh redirect back
    }
}