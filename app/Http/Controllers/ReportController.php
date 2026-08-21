<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $status = $request->input('status', 'all');
        $categoryId = $request->input('category_id', 'all');

        $query = Complaint::with(['category', 'assignedOfficer', 'user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        $complaints = $query->latest()->get();
        $categories = Category::where('is_active', true)->get();

        $stats = [
            'total' => $complaints->count(),
            'selesai' => $complaints->where('status', 'selesai')->count(),
            'diproses' => $complaints->whereIn('status', ['didisposisikan', 'diproses', 'menunggu_persetujuan'])->count(),
            'menunggu' => $complaints->where('status', 'menunggu_verifikasi')->count(),
            'ditolak' => $complaints->where('status', 'ditolak')->count(),
            'rating_avg' => round($complaints->whereNotNull('satisfaction_rating')->avg('satisfaction_rating') ?? 0, 1),
        ];

        return view('pages.dashboard.laporan.index', compact('complaints', 'categories', 'stats', 'startDate', 'endDate', 'status', 'categoryId'));
    }

    public function print(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $status = $request->input('status', 'all');
        $categoryId = $request->input('category_id', 'all');

        $query = Complaint::with(['category', 'assignedOfficer', 'user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        $complaints = $query->latest()->get();

        return view('pages.dashboard.laporan.print', compact('complaints', 'startDate', 'endDate', 'status'));
    }
}
