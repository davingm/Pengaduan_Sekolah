@extends('layouts.dashboard')

@section('title', 'Kelola Tiket: ' . $complaint->ticket_code)
@section('page_title', 'Detail & Penanganan Pengaduan')
@section('page_description', 'Kelola status, disposisi tugas, dan validasi tindak lanjut tiket pengaduan.')

@section('content')
<div class="space-y-8" x-data="{ 
    modalVerify: false, 
    modalReject: false, 
    modalResolve: false, 
    modalApprove: false, 
    modalRevision: false 
}">
    
    <!-- Top Action Bar & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('dashboard.pengaduan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar
        </a>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Link to Public View -->
            <a href="{{ route('pengaduan.show', $complaint->ticket_code) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>Tampilan Siswa</span>
            </a>

            <!-- Action: Guru Piket (Verifikasi / Tolak) -->
            @if((Auth::user()->isGuruPiket() || Auth::user()->isAdmin()) && $complaint->status === 'menunggu_verifikasi')
                <button @click="modalVerify = true" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/20 transition flex items-center gap-1.5">
                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                    <span>Verifikasi & Disposisi</span>
                </button>
                <button @click="modalReject = true" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-600/20 transition flex items-center gap-1.5">
                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                    <span>Tolak / Arsipkan</span>
                </button>
            @endif

            <!-- Action: Petugas (Mulai Proses / Selesaikan) -->
            @if((Auth::user()->isPetugas() || Auth::user()->isAdmin()) && in_array($complaint->status, ['didisposisikan', 'diproses']))
                @if($complaint->status === 'didisposisikan')
                    <form action="{{ route('dashboard.pengaduan.process', $complaint->ticket_code) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold shadow-lg shadow-sky-600/20 transition flex items-center gap-1.5">
                            <i data-lucide="play" class="w-3.5 h-3.5"></i>
                            <span>Mulai Tangani Kasus</span>
                        </button>
                    </form>
                @endif
                <button @click="modalResolve = true" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-lg shadow-blue-600/20 transition flex items-center gap-1.5">
                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                    <span>Laporkan Selesai (Ke Kepsek)</span>
                </button>
            @endif

            <!-- Action: Kepala Sekolah (Approve / Revisi) -->
            @if((Auth::user()->isKepalaSekolah() || Auth::user()->isAdmin()) && $complaint->status === 'menunggu_persetujuan')
                <button @click="modalApprove = true" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/20 transition flex items-center gap-1.5">
                    <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                    <span>Setujui & Tutup Kasus</span>
                </button>
                <button @click="modalRevision = true" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold shadow-lg shadow-amber-600/20 transition flex items-center gap-1.5">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Minta Revisi Petugas</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono text-sm font-bold bg-blue-950/60 text-blue-400 px-3 py-1 rounded-xl border border-blue-800">
                        {{ $complaint->ticket_code }}
                    </span>
                    <x-status-badge :status="$complaint->status" />
                    <x-priority-badge :priority="$complaint->priority" />
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-white pt-1">
                    {{ $complaint->title }}
                </h1>
            </div>

            <div class="text-left sm:text-right shrink-0 text-xs text-slate-400">
                <div>Tanggal Pengaduan:</div>
                <div class="font-semibold text-white">{{ $complaint->created_at->format('d M Y, H:i') }} WIB</div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800 space-y-2">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Informasi Pelapor</div>
                <div class="font-semibold text-white">
                    @if($complaint->is_anonymous)
                        <span class="text-amber-400 font-mono flex items-center gap-1">
                            <i data-lucide="eye-off" class="w-3.5 h-3.5"></i> Laporan Anonim (Siswa)
                        </span>
                    @else
                        {{ $complaint->reporter_name }}
                    @endif
                </div>
                <div class="text-slate-400">Kelas / Rombel: {{ $complaint->reporter_class ?? '-' }}</div>
                @if(!$complaint->is_anonymous && $complaint->reporter_phone)
                    <div class="text-slate-400">Kontak: {{ $complaint->reporter_phone }}</div>
                @endif
            </div>

            <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800 space-y-2">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kategori & Lokasi</div>
                <div class="font-semibold text-white flex items-center gap-1.5">
                    <i data-lucide="tag" class="w-3.5 h-3.5 text-blue-400"></i>
                    <span>{{ $complaint->category->name }}</span>
                </div>
                <div class="text-slate-400">Lokasi: <span class="text-slate-200">{{ $complaint->location ?? 'Tidak spesifik' }}</span></div>
                <div class="text-slate-400">Urgensi: <span class="text-slate-200 font-bold uppercase">{{ $complaint->priority }}</span></div>
            </div>

            <div class="bg-slate-950/60 p-4 rounded-2xl border border-slate-800 space-y-2">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Aktor & Penugasan</div>
                <div class="text-slate-400">Guru Verifikator: <span class="text-slate-200 font-semibold">{{ $complaint->assignedByGuru->name ?? '-' }}</span></div>
                <div class="text-slate-400">Petugas Ditugaskan: <span class="text-slate-200 font-semibold">{{ $complaint->assignedOfficer->name ?? 'Belum ada' }}</span></div>
                @if($complaint->resolved_at)
                    <div class="text-emerald-400 font-semibold">Tuntas: {{ $complaint->resolved_at->format('d M Y') }}</div>
                @endif
            </div>
        </div>

        <!-- Description Content -->
        <div class="space-y-2">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Uraian Kasus:</div>
            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 text-xs text-slate-200 leading-relaxed whitespace-pre-line">
                {{ $complaint->description }}
            </div>
        </div>

        <!-- Initial Evidence Attachments -->
        @if($complaint->evidenceAttachments->isNotEmpty())
            <div class="space-y-2">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Lampiran Bukti Awal Pelapor:</div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($complaint->evidenceAttachments as $att)
                        <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="block group relative rounded-2xl overflow-hidden border border-slate-800 bg-slate-950">
                            @if(Str::startsWith($att->file_type, 'image/'))
                                <img src="{{ asset('storage/' . $att->file_path) }}" alt="{{ $att->file_name }}" class="w-full h-28 object-cover group-hover:scale-105 transition">
                            @else
                                <div class="h-28 flex flex-col items-center justify-center p-2 text-center text-slate-500">
                                    <i data-lucide="file-text" class="w-8 h-8 mb-1 text-blue-500"></i>
                                    <span class="text-[10px] truncate max-w-full font-medium">{{ $att->file_name }}</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold">
                                Lihat Berkas
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Resolution Notes & Attachments by Petugas -->
        @if($complaint->resolution_notes || $complaint->resolutionAttachments->isNotEmpty() || $complaint->approval_notes)
            <div class="mt-6 p-6 rounded-3xl bg-blue-950/30 border border-blue-800/60 space-y-4">
                <div class="flex items-center gap-2 text-blue-300 font-bold text-sm">
                    <i data-lucide="file-check" class="w-5 h-5"></i>
                    <span>Laporan Hasil Tindak Lanjut & Bukti Penyelesaian Petugas</span>
                </div>

                @if($complaint->resolution_notes)
                    <div class="text-xs text-slate-200 leading-relaxed whitespace-pre-line bg-slate-950/70 p-4 rounded-2xl border border-blue-900/60">
                        <span class="font-bold block text-blue-400 mb-1">Berita Acara Penanganan Petugas:</span>
                        {{ $complaint->resolution_notes }}
                    </div>
                @endif

                @if($complaint->approval_notes)
                    <div class="text-xs text-slate-200 leading-relaxed whitespace-pre-line bg-slate-950/70 p-4 rounded-2xl border border-purple-900/60">
                        <span class="font-bold block text-purple-400 mb-1">Persetujuan & Pengesahan Kepala Sekolah:</span>
                        {{ $complaint->approval_notes }}
                    </div>
                @endif

                @if($complaint->resolutionAttachments->isNotEmpty())
                    <div class="space-y-1.5">
                        <div class="text-[11px] font-bold text-blue-300 uppercase">Foto Bukti Setelah Perbaikan / Selesai:</div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($complaint->resolutionAttachments as $att)
                                <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="block group relative rounded-2xl overflow-hidden border border-blue-800 bg-slate-950">
                                    <img src="{{ asset('storage/' . $att->file_path) }}" alt="{{ $att->file_name }}" class="w-full h-28 object-cover group-hover:scale-105 transition">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold">
                                        Bukti Tuntas
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </div>

    <!-- Timeline Logs & Discussion -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Activity Timeline Logs -->
        <div class="lg:col-span-6 space-y-4">
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <h3 class="font-bold text-sm text-white flex items-center gap-2">
                    <i data-lucide="history" class="w-4 h-4 text-blue-400"></i>
                    <span>Log Aktivitas & Audit Trail</span>
                </h3>

                <div class="space-y-5 relative before:absolute before:inset-0 before:left-3.5 before:w-0.5 before:bg-slate-800">
                    @foreach($complaint->logs as $log)
                        <div class="relative flex items-start gap-4">
                            <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold z-10 shrink-0 shadow-md">
                                <i data-lucide="circle-dot" class="w-3.5 h-3.5"></i>
                            </div>
                            <div class="flex-1 bg-slate-950/60 p-3.5 rounded-2xl border border-slate-800 text-xs">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-white">{{ $log->actor_name }} <span class="text-[10px] text-slate-400 font-normal">({{ $log->actor_role }})</span></span>
                                    <span class="text-[10px] text-slate-500">{{ $log->created_at->format('d M, H:i') }}</span>
                                </div>
                                <div class="text-slate-300 leading-relaxed">{{ $log->notes }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Responses & Notes (Staff Internal / Pelapor) -->
        <div class="lg:col-span-6 space-y-4">
            <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <h3 class="font-bold text-sm text-white flex items-center gap-2">
                    <i data-lucide="message-square" class="w-4 h-4 text-blue-400"></i>
                    <span>Catatan Internal & Pesan Pelapor</span>
                </h3>

                <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                    @forelse($complaint->responses as $res)
                        <div class="p-3.5 rounded-2xl text-xs {{ $res->is_internal ? 'bg-amber-950/30 border border-amber-800/50' : 'bg-slate-950/60 border border-slate-800' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-white flex items-center gap-1.5">
                                    <span>{{ $res->sender_name }}</span>
                                    <span class="text-[10px] text-slate-400">({{ $res->sender_role }})</span>
                                    @if($res->is_internal)
                                        <span class="bg-amber-500/20 text-amber-300 border border-amber-500/30 px-1.5 py-0.2 rounded text-[10px] font-bold">Internal</span>
                                    @endif
                                </span>
                                <span class="text-[10px] text-slate-500">{{ $res->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-300 leading-relaxed">{{ $res->message }}</p>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-500">
                            Belum ada catatan atau tanggapan.
                        </div>
                    @endforelse
                </div>

                <form action="{{ route('dashboard.pengaduan.response', $complaint->ticket_code) }}" method="POST" class="space-y-3 pt-3 border-t border-slate-800">
                    @csrf
                    <textarea name="message" rows="2" required placeholder="Tuliskan catatan tindak lanjut atau pesan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-400">
                            <input type="checkbox" name="is_internal" value="1" class="rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-blue-500">
                            <span>Catatan Rahasia Staf (Siswa tidak melihat)</span>
                        </label>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-4 py-2 rounded-xl text-xs transition flex items-center gap-1.5">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            <span>Kirim</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- MODAL 1: Guru Piket Verifikasi & Disposisi -->
    <div x-show="modalVerify" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-5" @click.outside="modalVerify = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2 text-white font-bold text-base">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>
                    <span>Verifikasi & Disposisikan Pengaduan</span>
                </div>
                <button @click="modalVerify = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('dashboard.pengaduan.verify', $complaint->ticket_code) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Tugaskan ke Petugas Terkait *</label>
                    <select name="assigned_to" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
                        <option value="">-- Pilih Petugas / Penanggung Jawab --</option>
                        @foreach($officers as $officer)
                            <option value="{{ $officer->id }}" {{ $complaint->assigned_to == $officer->id ? 'selected' : '' }}>
                                {{ $officer->name }} ({{ $officer->department ?? $officer->role_label }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Klasifikasi Kategori *</label>
                        <select name="category_id" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $complaint->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Tingkat Urgensi *</label>
                        <select name="priority" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-blue-500">
                            <option value="rendah" {{ $complaint->priority == 'rendah' ? 'selected' : '' }}>Rendah</option>
                            <option value="sedang" {{ $complaint->priority == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="tinggi" {{ $complaint->priority == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="darurat" {{ $complaint->priority == 'darurat' ? 'selected' : '' }}>Darurat</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Catatan Instruksi untuk Petugas (Opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Tolong prioritaskan pengecekan siang ini sebelum jam praktikum berikutnya..." class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="modalVerify = false" class="px-4 py-2 rounded-xl text-slate-400 hover:text-white text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/30">
                        Konfirmasi & Disposisikan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Guru Piket Tolak / Arsipkan -->
    <div x-show="modalReject" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-5" @click.outside="modalReject = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2 text-white font-bold text-base">
                    <i data-lucide="x-circle" class="w-5 h-5 text-rose-400"></i>
                    <span>Tolak & Arsipkan Pengaduan</span>
                </div>
                <button @click="modalReject = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('dashboard.pengaduan.reject', $complaint->ticket_code) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Alasan Penolakan / Pengarsipan *</label>
                    <textarea name="rejection_reason" rows="4" required placeholder="Jelaskan secara jelas mengapa laporan ini tidak valid (misal: di luar area sekolah, informasi fiktif/iseng, atau bukan kewenangan sekolah)..." class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="modalReject = false" class="px-4 py-2 rounded-xl text-slate-400 hover:text-white text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-lg shadow-rose-600/30">
                        Tolak & Arsipkan Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Petugas Buat Laporan Tindak Lanjut & Bukti Selesai -->
    <div x-show="modalResolve" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-5" @click.outside="modalResolve = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2 text-white font-bold text-base">
                    <i data-lucide="send" class="w-5 h-5 text-blue-400"></i>
                    <span>Laporan Penanganan Selesai (Ke Kepsek)</span>
                </div>
                <button @click="modalResolve = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('dashboard.pengaduan.resolve', $complaint->ticket_code) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Uraian Tindakan / Perbaikan yang Telah Dilakukan *</label>
                    <textarea name="resolution_notes" rows="4" required placeholder="Tuliskan berita acara tindakan (misal: penggantian bohlam LED baru, mediasi kedua belah pihak dengan konseling, perbaikan engsel pintu)..." class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-blue-500">{{ $complaint->resolution_notes }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Unggah Foto Bukti Hasil Penanganan (Opsional)</label>
                    <input type="file" name="resolution_attachments[]" multiple accept="image/*,.pdf" class="w-full bg-slate-950 border border-slate-700 rounded-xl p-2 text-xs text-slate-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="modalResolve = false" class="px-4 py-2 rounded-xl text-slate-400 hover:text-white text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30">
                        Serahkan ke Kepala Sekolah
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: Kepala Sekolah Setujui Penutupan Kasus -->
    <div x-show="modalApprove" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-5" @click.outside="modalApprove = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2 text-white font-bold text-base">
                    <i data-lucide="award" class="w-5 h-5 text-emerald-400"></i>
                    <span>Persetujuan & Penutupan Kasus (Kepala Sekolah)</span>
                </div>
                <button @click="modalApprove = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('dashboard.pengaduan.approve', $complaint->ticket_code) }}" method="POST" class="space-y-4">
                @csrf
                <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-800/60 text-xs text-emerald-300">
                    Menyetujui laporan ini akan mengubah status pengaduan menjadi <strong>Selesai</strong> dan menerbitkan salinan bukti tuntas kepada siswa pelapor.
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Catatan / Arahan Kepala Sekolah (Opsional)</label>
                    <textarea name="approval_notes" rows="3" placeholder="Laporan hasil penanganan telah ditinjau dan disetujui..." class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="modalApprove = false" class="px-4 py-2 rounded-xl text-slate-400 hover:text-white text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/30">
                        Sahkan & Tutup Kasus
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5: Kepala Sekolah Minta Revisi -->
    <div x-show="modalRevision" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-slate-900 border border-slate-700 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-5" @click.outside="modalRevision = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2 text-white font-bold text-base">
                    <i data-lucide="rotate-ccw" class="w-5 h-5 text-amber-400"></i>
                    <span>Kembalikan untuk Revisi Penanganan</span>
                </div>
                <button @click="modalRevision = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('dashboard.pengaduan.revision', $complaint->ticket_code) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 mb-1.5">Instruksi Tindak Lanjut Ulang / Revisi *</label>
                    <textarea name="revision_notes" rows="4" required placeholder="Jelaskan bagian mana yang belum memuaskan atau perlu ditinjau kembali oleh petugas..." class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-amber-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                    <button type="button" @click="modalRevision = false" class="px-4 py-2 rounded-xl text-slate-400 hover:text-white text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-lg shadow-amber-600/30">
                        Kirim Instruksi Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
