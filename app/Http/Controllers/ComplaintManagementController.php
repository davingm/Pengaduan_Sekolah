<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintLog;
use App\Models\ComplaintResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ComplaintManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Complaint::with(['category', 'assignedOfficer', 'user'])->latest();

        // If logged in as siswa, only show own complaints
        if ($user->isSiswa()) {
            $query->where('user_id', $user->id);
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Priority Filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search Filter
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('ticket_code', 'LIKE', "%{$q}%")
                    ->orWhere('title', 'LIKE', "%{$q}%")
                    ->orWhere('reporter_name', 'LIKE', "%{$q}%")
                    ->orWhere('location', 'LIKE', "%{$q}%");
            });
        }

        // Scope filter for petugas (e.g. "Tugas Saya" tab)
        if ($request->boolean('my_tasks') && $user->isPetugas()) {
            $query->where('assigned_to', $user->id);
        }

        $complaints = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        // Status counters for tab badges
        $statusCounts = [
            'all' => $user->isSiswa() ? Complaint::where('user_id', $user->id)->count() : Complaint::count(),
            'menunggu_verifikasi' => $user->isSiswa() ? Complaint::where('user_id', $user->id)->where('status', 'menunggu_verifikasi')->count() : Complaint::where('status', 'menunggu_verifikasi')->count(),
            'didisposisikan' => $user->isSiswa() ? Complaint::where('user_id', $user->id)->where('status', 'didisposisikan')->count() : Complaint::where('status', 'didisposisikan')->count(),
            'diproses' => $user->isSiswa() ? Complaint::where('user_id', $user->id)->where('status', 'diproses')->count() : Complaint::where('status', 'diproses')->count(),
            'menunggu_persetujuan' => $user->isSiswa() ? Complaint::where('user_id', $user->id)->where('status', 'menunggu_persetujuan')->count() : Complaint::where('status', 'menunggu_persetujuan')->count(),
            'selesai' => $user->isSiswa() ? Complaint::where('user_id', $user->id)->where('status', 'selesai')->count() : Complaint::where('status', 'selesai')->count(),
            'ditolak' => $user->isSiswa() ? Complaint::where('user_id', $user->id)->where('status', 'ditolak')->count() : Complaint::where('status', 'ditolak')->count(),
        ];

        return view('pages.dashboard.pengaduan.index', compact('complaints', 'categories', 'statusCounts'));
    }

    public function show(string $ticket_code)
    {
        $complaint = Complaint::with([
            'category',
            'assignedOfficer',
            'assignedByGuru',
            'user',
            'attachments',
            'evidenceAttachments',
            'resolutionAttachments',
            'logs.user',
            'responses.user',
        ])->where('ticket_code', $ticket_code)->firstOrFail();

        // Check permission if student
        if (Auth::user()->isSiswa() && $complaint->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pengaduan ini.');
        }

        // Available Officers for Assignment (Petugas, BK, Sarpras, Wali Kelas)
        $officers = User::whereIn('role', ['petugas', 'guru_piket', 'admin'])->get();
        $categories = Category::where('is_active', true)->get();

        return view('pages.dashboard.pengaduan.show', compact('complaint', 'officers', 'categories'));
    }

    /**
     * Guru Piket / Admin: Verifikasi dan Disposisi ke Petugas
     */
    public function verifyAndAssign(Request $request, string $ticket_code)
    {
        $complaint = Complaint::where('ticket_code', $ticket_code)->firstOrFail();

        if (!Auth::user()->isGuruPiket() && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Hanya Guru Piket atau Administrator yang dapat memverifikasi pengaduan.');
        }

        $validated = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'priority' => ['required', 'in:rendah,sedang,tinggi,darurat'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $assignedOfficer = User::findOrFail($validated['assigned_to']);

        $complaint->update([
            'category_id' => $validated['category_id'],
            'priority' => $validated['priority'],
            'assigned_to' => $assignedOfficer->id,
            'assigned_by' => Auth::id(),
            'status' => 'didisposisikan',
            'verified_at' => now(),
            'assigned_at' => now(),
        ]);

        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'user_id' => Auth::id(),
            'actor_name' => Auth::user()->name,
            'actor_role' => Auth::user()->role_label,
            'action' => 'didisposisikan',
            'status_from' => 'menunggu_verifikasi',
            'status_to' => 'didisposisikan',
            'notes' => 'Pengaduan diverifikasi valid & didisposisikan kepada: ' . $assignedOfficer->name . ($validated['notes'] ? ' | Catatan: ' . $validated['notes'] : ''),
        ]);

        return back()->with('success', 'Pengaduan berhasil diverifikasi dan didisposisikan kepada ' . $assignedOfficer->name . '.');
    }

    /**
     * Guru Piket / Admin: Tolak / Arsipkan pengaduan
     */
    public function reject(Request $request, string $ticket_code)
    {
        $complaint = Complaint::where('ticket_code', $ticket_code)->firstOrFail();

        if (!Auth::user()->isGuruPiket() && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Hanya Guru Piket atau Administrator yang dapat menolak pengaduan.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $complaint->update([
            'status' => 'ditolak',
            'assigned_by' => Auth::id(),
            'verified_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'user_id' => Auth::id(),
            'actor_name' => Auth::user()->name,
            'actor_role' => Auth::user()->role_label,
            'action' => 'ditolak',
            'status_from' => 'menunggu_verifikasi',
            'status_to' => 'ditolak',
            'notes' => 'Pengaduan ditolak & diarsipkan. Alasan: ' . $validated['rejection_reason'],
        ]);

        return back()->with('success', 'Pengaduan ditolak dan status diarsipkan.');
    }

    /**
     * Petugas: Mulai Tangani Kasus
     */
    public function startProcess(string $ticket_code)
    {
        $complaint = Complaint::where('ticket_code', $ticket_code)->firstOrFail();
        $user = Auth::user();

        if (!$user->isPetugas() && !$user->isAdmin()) {
            return back()->with('error', 'Akses ditolak.');
        }

        $complaint->update([
            'status' => 'diproses',
            'processed_at' => now(),
        ]);

        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'actor_name' => $user->name,
            'actor_role' => $user->role_label,
            'action' => 'diproses',
            'status_from' => 'didisposisikan',
            'status_to' => 'diproses',
            'notes' => 'Petugas mulai menangani tindakan lapangan / mediasi kasus.',
        ]);

        return back()->with('success', 'Status pengaduan diperbarui menjadi sedang diproses/ditangani.');
    }

    /**
     * Petugas: Buat Laporan Tindak Lanjut & Upload Bukti Selesai
     */
    public function submitResolution(Request $request, string $ticket_code)
    {
        $complaint = Complaint::where('ticket_code', $ticket_code)->firstOrFail();
        $user = Auth::user();

        if (!$user->isPetugas() && !$user->isAdmin()) {
            return back()->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'resolution_notes' => ['required', 'string', 'min:10', 'max:2000'],
            'resolution_attachments.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:5120'],
        ]);

        $complaint->update([
            'resolution_notes' => $validated['resolution_notes'],
            'status' => 'menunggu_persetujuan',
            'submitted_for_approval_at' => now(),
        ]);

        // Save resolution attachments
        if ($request->hasFile('resolution_attachments')) {
            foreach ($request->file('resolution_attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = 'res-' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . time() . '-' . Str::random(6) . '.' . $extension;
                $path = $file->storeAs('complaint_attachments', $filename, 'public');

                ComplaintAttachment::create([
                    'complaint_id' => $complaint->id,
                    'file_path' => $path,
                    'file_name' => $originalName,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'type' => 'resolution',
                ]);
            }
        }

        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'actor_name' => $user->name,
            'actor_role' => $user->role_label,
            'action' => 'diajukan_persetujuan',
            'status_from' => 'diproses',
            'status_to' => 'menunggu_persetujuan',
            'notes' => 'Laporan penanganan selesai dan diajukan ke Kepala Sekolah untuk persetujuan penutupan tiket.',
        ]);

        return back()->with('success', 'Laporan tindak lanjut berhasil diserahkan ke Kepala Sekolah untuk ditinjau.');
    }

    /**
     * Kepala Sekolah: Setujui Laporan Tindak Lanjut & Tutup Kasus
     */
    public function approveResolution(Request $request, string $ticket_code)
    {
        $complaint = Complaint::where('ticket_code', $ticket_code)->firstOrFail();
        $user = Auth::user();

        if (!$user->isKepalaSekala() && !$user->isKepalaSekolah() && !$user->isAdmin()) {
            return back()->with('error', 'Hanya Kepala Sekolah atau Administrator yang dapat menyetujui penutupan pengaduan.');
        }

        $validated = $request->validate([
            'approval_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $complaint->update([
            'status' => 'selesai',
            'resolved_at' => now(),
            'approval_notes' => $validated['approval_notes'] ?? 'Laporan tindak lanjut telah ditinjau dan disahkan oleh Kepala Sekolah.',
        ]);

        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'actor_name' => $user->name,
            'actor_role' => $user->role_label,
            'action' => 'disetujui',
            'status_from' => 'menunggu_persetujuan',
            'status_to' => 'selesai',
            'notes' => 'Kepala Sekolah menyetujui laporan tindak lanjut. Kasus resmi ditutup dan salinan bukti diserahkan ke pelapor.',
        ]);

        return back()->with('success', 'Pengaduan resmi disetujui dan ditutup. Siswa/pelapor dapat melihat salinan bukti penyelesaian.');
    }

    /**
     * Kepala Sekolah: Minta Revisi / Penanganan Ulang
     */
    public function requestRevision(Request $request, string $ticket_code)
    {
        $complaint = Complaint::where('ticket_code', $ticket_code)->firstOrFail();
        $user = Auth::user();

        if (!$user->isKepalaSekolah() && !$user->isAdmin()) {
            return back()->with('error', 'Hanya Kepala Sekolah yang dapat meminta revisi penanganan.');
        }

        $validated = $request->validate([
            'revision_notes' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $complaint->update([
            'status' => 'diproses',
        ]);

        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'actor_name' => $user->name,
            'actor_role' => $user->role_label,
            'action' => 'minta_revisi',
            'status_from' => 'menunggu_persetujuan',
            'status_to' => 'diproses',
            'notes' => 'Kepala Sekolah meminta revisi/tindakan lanjutan: ' . $validated['revision_notes'],
        ]);

        return back()->with('success', 'Instruksi revisi penanganan telah dikirim kembali ke petugas terkait.');
    }

    /**
     * Tanggapan / Diskusi Internal atau Publik
     */
    public function addResponse(Request $request, string $ticket_code)
    {
        $complaint = Complaint::where('ticket_code', $ticket_code)->firstOrFail();
        $user = Auth::user();

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:2000'],
            'is_internal' => ['nullable', 'boolean'],
        ]);

        ComplaintResponse::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'sender_name' => $user->name,
            'sender_role' => $user->role_label,
            'message' => $validated['message'],
            'is_internal' => $request->boolean('is_internal'),
        ]);

        return back()->with('success', 'Catatan/tanggapan berhasil ditambahkan.');
    }
}
