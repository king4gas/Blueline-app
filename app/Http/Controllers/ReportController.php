<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Mengambil data riwayat laporan user (format JSON untuk Widget)
    public function history()
    {
        $reports = Report::where('user_id', auth()->id())->latest()->get();
        return response()->json($reports);
    }

    // Menyimpan laporan baru
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        Report::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'pending'
        ]);

        return back()->with('message', 'Laporan berhasil dikirim! Tim kami akan segera mengeceknya.');
    }
}