<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('user')->latest()->get();
        return Inertia::render('Admin/Reports/Index', [
            'reports' => $reports
        ]);
    }

    public function update(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:pending,progress,done',
            'admin_note' => 'nullable|string'
        ]);

        $report->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note
        ]);

        return back()->with('message', 'Status laporan berhasil diperbarui.');
    }
}