<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComplaintController extends Controller
{
    // Tampilkan Daftar Laporan
    public function index()
    {
        $complaints = Complaint::with('user') // Ambil data user pelapor
            ->latest()
            ->get();

        return Inertia::render('Admin/Complaints/Index', [
            'complaints' => $complaints
        ]);
    }

    // Update Status Laporan
    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,resolved'
        ]);

        $complaint->update([
            'status' => $request->status
        ]);

        return back()->with('message', 'Status laporan berhasil diperbarui.');
    }

    // Hapus Laporan
    public function destroy(Complaint $complaint)
    {
        $complaint->delete();
        return back()->with('message', 'Laporan berhasil dihapus.');
    }
}