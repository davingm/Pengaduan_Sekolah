<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintLog;
use App\Models\ComplaintResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicComplaintController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->withCount('complaints')->get();
        
        $stats = [
            'total' => Complaint::count(),
            'selesai' => Complaint::where('status', 'selesai')->count(),
            'diproses' => Complaint::whereIn('status', ['didisposisikan', 'diproses', 'menunggu_persetujuan'])->count(),
            'menunggu' => Complaint::where('status', 'menunggu_verifikasi')->count(),
        ];

        $recentComplaints = Complaint::with('category')
            ->where('is_anonymous', false)
            ->latest()
            ->take(6)
            ->get();

        return view('pages.index', compact('categories', 'stats', 'recentComplaints'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('pages.pengaduan.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'location' => ['nullable', 'string', 'max:255'],
            'priority' => ['required', 'in:rendah,sedang,tinggi,darurat'],
            'is_anonymous' => ['nullable', 'boolean'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:5120'], // Max 5MB per file
        ];

        if (!Auth::check()) {
            // reporter_name wajib hanya jika tidak anonim
            $isAnonymousReq = $request->boolean('is_anonymous');
            $rules['reporter_name'] = $isAnonymousReq
                ? ['nullable', 'string', 'max:255']
                : ['required', 'string', 'max:255'];
            $rules['reporter_nisn'] = ['nullable', 'string', 'max:50'];
            $rules['reporter_phone'] = ['nullable', 'string', 'max:20'];
            $rules['reporter_email'] = ['nullable', 'email', 'max:255'];
            $rules['reporter_class'] = ['nullable', 'string', 'max:50'];
        }

        $validated = $request->validate($rules);

        // Generate unique ticket code: PGD-YYYYMM-XXXX
        $datePrefix = 'PGD-' . date('Ym') . '-';
        $latest = Complaint::where('ticket_code', 'LIKE', $datePrefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($latest) {
            $lastNum = (int) substr($latest->ticket_code, -4);
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }
        $ticketCode = $datePrefix . $newNum;

        $isAnonymous = $request->boolean('is_anonymous');
        $user = Auth::user();

        $complaint = Complaint::create([
            'ticket_code' => $ticketCode,
            'user_id' => $user?->id,
            'reporter_name' => $user
                ? $user->name
                : ($isAnonymous ? 'Anonim' : ($validated['reporter_name'] ?? 'Anonim')),
            'reporter_nisn' => $user ? $user->nisn_nip : ($validated['reporter_nisn'] ?? null),
            'reporter_phone' => $user ? $user->phone : ($validated['reporter_phone'] ?? null),
            'reporter_email' => $user ? $user->email : ($validated['reporter_email'] ?? null),
            'reporter_class' => $user ? $user->department : ($validated['reporter_class'] ?? null),
            'is_anonymous' => $isAnonymous,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'] ?? null,
            'priority' => $validated['priority'],
            'status' => 'menunggu_verifikasi',
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . time() . '-' . Str::random(6) . '.' . $extension;
                $path = $file->storeAs('complaint_attachments', $filename, 'public');

                ComplaintAttachment::create([
                    'complaint_id' => $complaint->id,
                    'file_path' => $path,
                    'file_name' => $originalName,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'type' => 'evidence',
                ]);
            }
        }

        // Activity log
        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user?->id,
            'actor_name' => $isAnonymous ? 'Siswa / Pelapor (Anonim)' : ($user ? $user->name : ($validated['reporter_name'] ?? 'Pelapor')),
            'actor_role' => $user ? $user->role_label : 'Pelapor Tamu',
            'action' => 'dibuat',
            'status_from' => null,
            'status_to' => 'menunggu_verifikasi',
            'notes' => 'Pengaduan berhasil diserahkan ke sistem untuk diverifikasi Guru Piket.',
        ]);

        return redirect()->route('pengaduan.show', $complaint->ticket_code)
            ->with('success', 'Pengaduan berhasil dikirim! Simpan Kode Tiket: ' . $complaint->ticket_code . ' untuk melacak status laporan Anda.');
    }

    public function track(Request $request)
    {
        $ticket = trim($request->input('ticket'));
        if (!$ticket) {
            return view('pages.pengaduan.track');
        }

        $complaint = Complaint::where('ticket_code', $ticket)->first();

        if (!$complaint) {
            return redirect()->route('pengaduan.track')
                ->with('error', 'Kode tiket pengaduan "' . $ticket . '" tidak ditemukan. Silakan periksa kembali.');
        }

        return redirect()->route('pengaduan.show', $complaint->ticket_code);
    }

    public function show(string $ticket_code)
    {
        $complaint = Complaint::with([
            'category',
            'assignedOfficer',
            'assignedByGuru',
            'attachments',
            'logs',
            'responses',
        ])->where('ticket_code', $ticket_code)->firstOrFail();

        return view('pages.pengaduan.show', compact('complaint'));
    }

    public function rate(Request $request, string $ticket_code)
    {
        $complaint = Complaint::where('ticket_code', $ticket_code)->firstOrFail();

        if ($complaint->status !== 'selesai') {
            return back()->with('error', 'Penilaian hanya dapat diberikan untuk pengaduan yang telah selesai.');
        }

        $validated = $request->validate([
            'satisfaction_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'satisfaction_feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $complaint->update([
            'satisfaction_rating' => $validated['satisfaction_rating'],
            'satisfaction_feedback' => $validated['satisfaction_feedback'] ?? null,
            'feedback_submitted_at' => now(),
        ]);

        ComplaintLog::create([
            'complaint_id' => $complaint->id,
            'user_id' => Auth::id(),
            'actor_name' => Auth::check() ? Auth::user()->name : 'Pelapor',
            'actor_role' => 'Pelapor',
            'action' => 'feedback_diberikan',
            'status_from' => 'selesai',
            'status_to' => 'selesai',
            'notes' => 'Memberikan ulasan kepuasan ' . $validated['satisfaction_rating'] . '/5 Bintang.',
        ]);

        return back()->with('success', 'Terima kasih atas ulasan dan penilaian yang Anda berikan!');
    }

    public function addResponse(Request $request, string $ticket_code)
    {
        $complaint = Complaint::where('ticket_code', $ticket_code)->firstOrFail();

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:3', 'max:2000'],
            'sender_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();
        $senderName = $user ? $user->name : ($validated['sender_name'] ?? 'Pelapor');
        $senderRole = $user ? $user->role_label : 'Pelapor';

        ComplaintResponse::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user?->id,
            'sender_name' => $senderName,
            'sender_role' => $senderRole,
            'message' => $validated['message'],
            'is_internal' => false,
        ]);

        return back()->with('success', 'Tanggapan / pesan Anda berhasil dikirim.');
    }
}
