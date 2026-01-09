<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    // 1. Tampilkan Halaman Tentang Kami
    public function about()
    {
        return Inertia::render('About');
    }

    // 2. Simpan Kritik & Saran
    public function storeFeedback(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'message' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Feedback::create($request->all());

        return back()->with('message', 'Terima kasih atas masukan Anda!');
    }
}