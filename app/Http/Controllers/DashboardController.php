<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Base metrics
        $query = Complaint::query();

        if ($user->isSiswa()) {
            $myComplaints = Complaint::with(['category', 'assignedOfficer'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(10)
                ->get();

            $stats = [
                'total' => Complaint::where('user_id', $user->id)->count(),
                'menunggu' => Complaint::where('user_id', $user->id)->where('status', 'menunggu_verifikasi')->count(),
                'diproses' => Complaint::where('user_id', $user->id)->whereIn('status', ['didisposisikan', 'diproses', 'menunggu_persetujuan'])->count(),
                'selesai' => Complaint::where('user_id', $user->id)->where('status', 'selesai')->count(),
            ];

            return view('pages.dashboard.index', compact('stats', 'myComplaints'));
        }

        // Staff / Piket / Petugas / Kepsek / Admin Dashboard
        $stats = [
            'total' => Complaint::count(),
            'menunggu_verifikasi' => Complaint::where('status', 'menunggu_verifikasi')->count(),
            'didisposisikan' => Complaint::where('status', 'didisposisikan')->count(),
            'diproses' => Complaint::where('status', 'diproses')->count(),
            'menunggu_persetujuan' => Complaint::where('status', 'menunggu_persetujuan')->count(),
            'selesai' => Complaint::where('status', 'selesai')->count(),
            'ditolak' => Complaint::where('status', 'ditolak')->count(),
            'tugas_saya' => $user->isPetugas() ? Complaint::where('assigned_to', $user->id)->whereIn('status', ['didisposisikan', 'diproses'])->count() : 0,
        ];

        // Action-needed lists based on role
        if ($user->isGuruPiket()) {
            $priorityTasks = Complaint::with('category')
                ->where('status', 'menunggu_verifikasi')
                ->orderByRaw("CASE priority WHEN 'darurat' THEN 1 WHEN 'tinggi' THEN 2 WHEN 'sedang' THEN 3 ELSE 4 END")
                ->latest()
                ->take(8)
                ->get();
        } elseif ($user->isPetugas()) {
            $priorityTasks = Complaint::with('category')
                ->where('assigned_to', $user->id)
                ->whereIn('status', ['didisposisikan', 'diproses'])
                ->orderByRaw("CASE priority WHEN 'darurat' THEN 1 WHEN 'tinggi' THEN 2 WHEN 'sedang' THEN 3 ELSE 4 END")
                ->latest()
                ->take(8)
                ->get();
        } elseif ($user->isKepalaSekolah()) {
            $priorityTasks = Complaint::with(['category', 'assignedOfficer'])
                ->where('status', 'menunggu_persetujuan')
                ->latest()
                ->take(8)
                ->get();
        } else { // Admin
            $priorityTasks = Complaint::with(['category', 'assignedOfficer'])
                ->latest()
                ->take(8)
                ->get();
        }

        $categoryDistribution = Category::withCount('complaints')->get();
        $recentComplaints = Complaint::with(['category', 'assignedOfficer'])->latest()->take(5)->get();

        return view('pages.dashboard.index', compact('stats', 'priorityTasks', 'categoryDistribution', 'recentComplaints'));
    }
}
