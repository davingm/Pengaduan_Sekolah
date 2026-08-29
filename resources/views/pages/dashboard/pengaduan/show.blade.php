@extends('layouts.dashboard')

@section('title', 'Kelola Tiket: ' . $complaint->ticket_code)
@section('page_title', 'Detail Pengaduan')
@section('page_description', 'Kelola alur disposisi, penanganan lapangan, dan verifikasi status tiket.')

@section('content')
<div class="space-y-6" x-data="{ 
    modalVerify: false, 
    modalReject: false, 
    modalResolve: false, 
    modalApprove: false, 
    modalRevision: false 
}">
    
    <!-- Top Action Bar & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-zinc-800/80">
        <a href="{{ route('dashboard.pengaduan.index') }}" 
           class="inline-flex items-center gap-1.5 text-xs font-mono font-medium text-zinc-400 hover:text-zinc-100 transition-colors">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Kembali ke Daftar Pengaduan</span>
        </a>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Link to Public View -->
            <a href="{{ route('pengaduan.show', $complaint->ticket_code) }}" target="_blank" 
               class="px-3 py-1.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-zinc-100 text-xs font-medium border border-zinc-800 transition-colors flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-3.5 h-3.5 text-zinc-500"></i>
                <span>Tampilan Siswa</span>
            </a>
        </div>
    </div>

    <!-- Prominent Role Action Callout (Teacher & Staff Friendly UX) -->
    @if((Auth::user()->isGuruPiket() || Auth::user()->isAdmin()) && $complaint->status === 'menunggu_verifikasi')
        <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-amber-400 font-semibold text-xs">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    <span>Tindakan Diperlukan (Guru Piket / BK)</span>
                </div>
                <p class="text-xs text-zinc-300 leading-relaxed">
                    Pengaduan baru ini menunggu verifikasi kelayakan dari Anda sebelum diteruskan ke petugas penanganan lapangan.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button @click="modalVerify = true" 
                        class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium shadow-sm transition-all flex items-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Verifikasi & Disposisi</span>
                </button>
                <button @click="modalReject = true" 
                        class="px-3.5 py-2 rounded-lg bg-zinc-900 hover:bg-red-500/10 text-zinc-300 hover:text-red-400 text-xs font-medium border border-zinc-800 hover:border-red-500/30 transition-all flex items-center gap-1.5">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    <span>Tolak Laporan</span>
                </button>
            </div>
        </div>
    @endif

    @if((Auth::user()->isPetugas() || Auth::user()->isAdmin()) && $complaint->status === 'didisposisikan')
        <div class="bg-orange-500/10 border border-orange-500/30 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-orange-400 font-semibold text-xs">
                    <i data-lucide="wrench" class="w-4 h-4"></i>
                    <span>Tugas Penanganan Baru (Petugas Lapangan)</span>
                </div>
                <p class="text-xs text-zinc-300 leading-relaxed">
                    Laporan ini telah didisposisikan ke Anda. Klik tombol di samping untuk mengonfirmasi bahwa Anda mulai menangani kasus ini.
                </p>
            </div>
            <div class="shrink-0">
                <form action="{{ route('dashboard.pengaduan.process', $complaint->ticket_code) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium shadow-sm transition-all flex items-center gap-1.5">
                        <i data-lucide="play" class="w-3.5 h-3.5"></i>
                        <span>Mulai Tangani Kasus</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    @if((Auth::user()->isPetugas() || Auth::user()->isAdmin()) && $complaint->status === 'diproses')
        <div class="bg-orange-500/10 border border-orange-500/30 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-orange-400 font-semibold text-xs">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    <span>Sedang Ditangani (Petugas Lapangan)</span>
                </div>
                <p class="text-xs text-zinc-300 leading-relaxed">
                    Jika perbaikan fisik atau tindakan telah selesai, silakan kirimkan berita acara dan foto bukti ke Kepala Sekolah.
                </p>
            </div>
            <div class="shrink-0">
                <button @click="modalResolve = true" 
                        class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium shadow-sm transition-all flex items-center gap-1.5">
                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                    <span>Laporkan Selesai (Ke Kepsek)</span>
                </button>
            </div>
        </div>
    @endif

    @if((Auth::user()->isKepalaSekolah() || Auth::user()->isAdmin()) && $complaint->status === 'menunggu_persetujuan')
        <div class="bg-purple-500/10 border border-purple-500/30 rounded-xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-purple-300 font-semibold text-xs">
                    <i data-lucide="award" class="w-4 h-4"></i>
                    <span>Menunggu Pengesahan Akhir (Kepala Sekolah)</span>
                </div>
                <p class="text-xs text-zinc-300 leading-relaxed">
                    Petugas telah menyerahkan laporan hasil perbaikan. Silakan tinjau hasil penanganan dan berikan pengesahan penutupan kasus.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button @click="modalApprove = true" 
                        class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-medium shadow-sm transition-all flex items-center gap-1.5">
                    <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                    <span>Sahkan & Tutup Kasus</span>
                </button>
                <button @click="modalRevision = true" 
                        class="px-3.5 py-2 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-orange-400 text-xs font-medium border border-zinc-800 transition-all flex items-center gap-1.5">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    <span>Minta Revisi</span>
                </button>
            </div>
        </div>
    @endif

    @if($complaint->status === 'selesai')
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
            </div>
            <div>
                <div class="text-xs font-semibold text-emerald-300">Pengaduan Telah Selesai & Disahkan</div>
                <div class="text-[11px] text-zinc-400">Kasus ini telah diselesaikan oleh petugas dan disahkan resmi oleh Kepala Sekolah pada {{ $complaint->resolved_at ? $complaint->resolved_at->format('d F Y, H:i') : '-' }} WIB.</div>
            </div>
        </div>
    @endif

    @if($complaint->status === 'ditolak')
        <div class="bg-zinc-900/60 border border-zinc-800 rounded-xl p-4 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-500/10 text-red-400 flex items-center justify-center shrink-0 mt-0.5">
                <i data-lucide="x-circle" class="w-4 h-4"></i>
            </div>
            <div class="space-y-1">
                <div class="text-xs font-semibold text-zinc-200">Pengaduan Telah Ditolak / Diarsipkan</div>
                <div class="text-xs text-zinc-400 leading-relaxed">
                    <span class="font-mono text-zinc-500">Alasan:</span> {{ $complaint->rejection_reason ?? 'Tidak memenuhi syarat pengaduan sekolah.' }}
                </div>
            </div>
        </div>
    @endif

    <!-- Main Detail Card (Sheaf UI Card) -->
    <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-5 sm:p-6 space-y-6">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-800/80">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono text-xs font-semibold bg-zinc-900 text-zinc-200 px-2 py-0.5 rounded border border-zinc-800">
                        {{ $complaint->ticket_code }}
                    </span>
                    <x-status-badge :status="$complaint->status" />
                    <x-priority-badge :priority="$complaint->priority" />
                </div>
                <h1 class="text-lg sm:text-xl font-semibold text-zinc-100">
                    {{ $complaint->title }}
                </h1>
            </div>

            <div class="text-left sm:text-right shrink-0 text-xs font-mono text-zinc-500">
                <div>Waktu Pengaduan:</div>
                <div class="font-medium text-zinc-300">{{ $complaint->created_at->format('d M Y, H:i') }} WIB</div>
            </div>
        </div>

        <!-- 3-Column Info Grid (Sheaf Sub-cards) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
            <div class="bg-zinc-900/40 p-4 rounded-lg border border-zinc-800/80 space-y-2">
                <div class="text-[10px] font-mono font-semibold text-zinc-500 uppercase tracking-wider">Informasi Pelapor</div>
                <div class="font-medium text-zinc-200">
                    @if($complaint->is_anonymous)
                        <span class="text-orange-400 font-mono flex items-center gap-1.5">
                            <i data-lucide="eye-off" class="w-3.5 h-3.5"></i> Laporan Anonim
                        </span>
                    @else
                        {{ $complaint->reporter_name }}
                    @endif
                </div>
                <div class="text-zinc-400 font-mono">Kelas: {{ $complaint->reporter_class ?? '-' }}</div>
                @if(!$complaint->is_anonymous && $complaint->reporter_phone)
                    <div class="text-zinc-400 font-mono">Kontak: {{ $complaint->reporter_phone }}</div>
                @endif
            </div>

            <div class="bg-zinc-900/40 p-4 rounded-lg border border-zinc-800/80 space-y-2">
                <div class="text-[10px] font-mono font-semibold text-zinc-500 uppercase tracking-wider">Kategori & Lokasi</div>
                <div class="font-medium text-zinc-200 flex items-center gap-1.5">
                    <i data-lucide="tag" class="w-3.5 h-3.5 text-orange-400"></i>
                    <span>{{ $complaint->category->name }}</span>
                </div>
                <div class="text-zinc-400 font-mono">Lokasi: <span class="text-zinc-200">{{ $complaint->location ?? 'Tidak spesifik' }}</span></div>
                <div class="text-zinc-400 font-mono">Tingkat: <span class="text-zinc-200 uppercase">{{ $complaint->priority }}</span></div>
            </div>

            <div class="bg-zinc-900/40 p-4 rounded-lg border border-zinc-800/80 space-y-2">
                <div class="text-[10px] font-mono font-semibold text-zinc-500 uppercase tracking-wider">Aktor & Penugasan</div>
                <div class="text-zinc-400 font-mono">Verifikator: <span class="text-zinc-200">{{ $complaint->assignedByGuru->name ?? '-' }}</span></div>
                <div class="text-zinc-400 font-mono">Petugas: <span class="text-zinc-200 font-medium">{{ $complaint->assignedOfficer->name ?? 'Belum ada' }}</span></div>
                @if($complaint->resolved_at)
                    <div class="text-emerald-400 font-mono font-medium">Tuntas: {{ $complaint->resolved_at->format('d M Y') }}</div>
                @endif
            </div>
        </div>

        <!-- Description Content -->
        <div class="space-y-2">
            <div class="text-[11px] font-mono font-semibold text-zinc-500 uppercase tracking-wider">Uraian Masalah:</div>
            <div class="p-4 rounded-lg bg-zinc-900/40 border border-zinc-800/80 text-xs text-zinc-300 leading-relaxed whitespace-pre-line font-sans">
                {{ $complaint->description }}
            </div>
        </div>

        <!-- Initial Evidence Attachments -->
        @if($complaint->evidenceAttachments->isNotEmpty())
            <div class="space-y-2.5">
                <div class="text-[11px] font-mono font-semibold text-zinc-500 uppercase tracking-wider">Lampiran Bukti Awal:</div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($complaint->evidenceAttachments as $att)
                        <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" 
                           class="block group relative rounded-lg overflow-hidden border border-zinc-800 bg-zinc-900/60 hover:border-zinc-700 transition-colors">
                            @if(Str::startsWith($att->file_type, 'image/'))
                                <img src="{{ asset('storage/' . $att->file_path) }}" alt="{{ $att->file_name }}" class="w-full h-24 object-cover group-hover:opacity-90 transition">
                            @else
                                <div class="h-24 flex flex-col items-center justify-center p-2 text-center text-zinc-400">
                                    <i data-lucide="file-text" class="w-6 h-6 mb-1 text-orange-400"></i>
                                    <span class="text-[10px] font-mono truncate max-w-full">{{ $att->file_name }}</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-mono">
                                Lihat Berkas &rarr;
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Resolution Notes & Attachments by Petugas -->
        @if($complaint->resolution_notes || $complaint->resolutionAttachments->isNotEmpty() || $complaint->approval_notes)
            <div class="p-5 rounded-lg bg-zinc-900/40 border border-zinc-800/90 space-y-4">
                <div class="flex items-center gap-2 text-zinc-200 font-semibold text-xs">
                    <i data-lucide="check-circle" class="w-4 h-4 text-orange-400"></i>
                    <span>Berita Acara & Tindak Lanjut Penanganan</span>
                </div>

                @if($complaint->resolution_notes)
                    <div class="text-xs text-zinc-300 leading-relaxed whitespace-pre-line bg-zinc-950/80 p-3.5 rounded-lg border border-zinc-800">
                        <span class="font-mono text-[11px] font-semibold block text-orange-400 mb-1">Tindakan Petugas Lapangan:</span>
                        {{ $complaint->resolution_notes }}
                    </div>
                @endif

                @if($complaint->approval_notes)
                    <div class="text-xs text-zinc-300 leading-relaxed whitespace-pre-line bg-zinc-950/80 p-3.5 rounded-lg border border-zinc-800">
                        <span class="font-mono text-[11px] font-semibold block text-emerald-400 mb-1">Persetujuan Kepala Sekolah:</span>
                        {{ $complaint->approval_notes }}
                    </div>
                @endif

                @if($complaint->resolutionAttachments->isNotEmpty())
                    <div class="space-y-2">
                        <div class="text-[10px] font-mono font-semibold text-zinc-500 uppercase tracking-wider">Foto Bukti Setelah Perbaikan:</div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($complaint->resolutionAttachments as $att)
                                <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" 
                                   class="block group relative rounded-lg overflow-hidden border border-zinc-800 bg-zinc-950 hover:border-zinc-700 transition">
                                    <img src="{{ asset('storage/' . $att->file_path) }}" alt="{{ $att->file_name }}" class="w-full h-24 object-cover group-hover:opacity-90 transition">
                                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-mono">
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

    <!-- Timeline Logs & Internal Discussion (Sheaf 2-Column Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Activity Timeline Logs (Cloudflare Audit Trail) -->
        <div class="lg:col-span-6 space-y-4">
            <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-5 space-y-4">
                <h3 class="font-semibold text-xs text-zinc-200 flex items-center gap-2">
                    <i data-lucide="history" class="w-3.5 h-3.5 text-zinc-400"></i>
                    <span>Log Aktivitas & Audit Trail</span>
                </h3>

                <div class="space-y-4 relative before:absolute before:inset-0 before:left-3 before:w-px before:bg-zinc-800">
                    @foreach($complaint->logs as $log)
                        <div class="relative flex items-start gap-3.5">
                            <div class="w-6 h-6 rounded-full bg-zinc-900 border border-zinc-700 text-orange-400 flex items-center justify-center text-[10px] font-mono z-10 shrink-0">
                                <i data-lucide="circle-dot" class="w-3 h-3"></i>
                            </div>
                            <div class="flex-1 bg-zinc-900/40 p-3 rounded-lg border border-zinc-800/80 text-xs">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-medium text-zinc-200">{{ $log->actor_name }} <span class="text-[10px] font-mono text-zinc-500 font-normal">({{ $log->actor_role }})</span></span>
                                    <span class="text-[10px] font-mono text-zinc-500">{{ $log->created_at->format('d M, H:i') }}</span>
                                </div>
                                <div class="text-zinc-400 leading-relaxed">{{ $log->notes }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Internal Notes & Responses -->
        <div class="lg:col-span-6 space-y-4">
            <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-5 space-y-4">
                <h3 class="font-semibold text-xs text-zinc-200 flex items-center gap-2">
                    <i data-lucide="message-square" class="w-3.5 h-3.5 text-zinc-400"></i>
                    <span>Catatan Internal & Pesan Pelapor</span>
                </h3>

                <div class="space-y-2.5 max-h-80 overflow-y-auto pr-1">
                    @forelse($complaint->responses as $res)
                        <div class="p-3 rounded-lg text-xs {{ $res->is_internal ? 'bg-orange-500/5 border border-orange-500/20' : 'bg-zinc-900/40 border border-zinc-800/80' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-medium text-zinc-200 flex items-center gap-1.5">
                                    <span>{{ $res->sender_name }}</span>
                                    <span class="text-[10px] font-mono text-zinc-500">({{ $res->sender_role }})</span>
                                    @if($res->is_internal)
                                        <span class="bg-orange-500/20 text-orange-400 border border-orange-500/30 px-1 py-0.2 rounded text-[9px] font-mono font-medium">Internal</span>
                                    @endif
                                </span>
                                <span class="text-[10px] font-mono text-zinc-500">{{ $res->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-zinc-300 leading-relaxed">{{ $res->message }}</p>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs font-mono text-zinc-600">
                            Belum ada catatan atau tanggapan.
                        </div>
                    @endforelse
                </div>

                <form action="{{ route('dashboard.pengaduan.response', $complaint->ticket_code) }}" method="POST" class="space-y-3 pt-3 border-t border-zinc-800/80">
                    @csrf
                    <textarea name="message" rows="2" required placeholder="Tuliskan catatan tindak lanjut atau pesan..." class="w-full bg-zinc-900/80 border border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-200 placeholder:text-zinc-600 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500"></textarea>
                    
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-zinc-400 select-none">
                            <input type="checkbox" name="is_internal" value="1" class="rounded bg-zinc-900 border-zinc-700 text-orange-500 focus:ring-orange-500">
                            <span class="font-mono text-[11px]">Catatan Internal (Rahasia)</span>
                        </label>

                        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-medium px-3.5 py-1.5 rounded-lg text-xs transition-all flex items-center gap-1.5 shadow-sm">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            <span>Kirim</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- MODAL 1: Guru Piket Verifikasi & Disposisi (Sheaf UI Dialog) -->
    <div x-show="modalVerify" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-zinc-950 border border-zinc-800 rounded-xl p-5 max-w-lg w-full shadow-2xl space-y-4" @click.outside="modalVerify = false">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
                <div class="flex items-center gap-2 text-zinc-100 font-semibold text-sm">
                    <i data-lucide="check-circle" class="w-4 h-4 text-orange-400"></i>
                    <span>Verifikasi & Disposisikan Pengaduan</span>
                </div>
                <button @click="modalVerify = false" class="text-zinc-500 hover:text-zinc-300 p-1"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>

            <form action="{{ route('dashboard.pengaduan.verify', $complaint->ticket_code) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1.5">Tugaskan ke Petugas Terkait *</label>
                    <select name="assigned_to" required class="w-full bg-zinc-900 border border-zinc-800 rounded-lg px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-orange-500">
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
                        <label class="block text-xs font-medium text-zinc-300 mb-1.5">Klasifikasi Kategori *</label>
                        <select name="category_id" required class="w-full bg-zinc-900 border border-zinc-800 rounded-lg px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-orange-500">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $complaint->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-300 mb-1.5">Tingkat Urgensi *</label>
                        <select name="priority" required class="w-full bg-zinc-900 border border-zinc-800 rounded-lg px-3 py-2 text-xs text-zinc-100 focus:outline-none focus:border-orange-500">
                            <option value="rendah" {{ $complaint->priority == 'rendah' ? 'selected' : '' }}>Rendah</option>
                            <option value="sedang" {{ $complaint->priority == 'sedang' ? 'selected' : '' }}>Sedang</option>
                            <option value="tinggi" {{ $complaint->priority == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                            <option value="darurat" {{ $complaint->priority == 'darurat' ? 'selected' : '' }}>Darurat</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1.5">Catatan Instruksi untuk Petugas (Opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Tolong prioritaskan pengecekan siang ini sebelum jam praktikum berikutnya..." class="w-full bg-zinc-900 border border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:border-orange-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-800">
                    <button type="button" @click="modalVerify = false" class="px-3 py-1.5 rounded-lg text-zinc-400 hover:text-zinc-200 text-xs transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium text-xs shadow-sm transition">
                        Konfirmasi & Disposisikan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Guru Piket Tolak / Arsipkan -->
    <div x-show="modalReject" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-zinc-950 border border-zinc-800 rounded-xl p-5 max-w-lg w-full shadow-2xl space-y-4" @click.outside="modalReject = false">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
                <div class="flex items-center gap-2 text-zinc-100 font-semibold text-sm">
                    <i data-lucide="x-circle" class="w-4 h-4 text-red-400"></i>
                    <span>Tolak & Arsipkan Pengaduan</span>
                </div>
                <button @click="modalReject = false" class="text-zinc-500 hover:text-zinc-300 p-1"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>

            <form action="{{ route('dashboard.pengaduan.reject', $complaint->ticket_code) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1.5">Alasan Penolakan / Pengarsipan *</label>
                    <textarea name="rejection_reason" rows="4" required placeholder="Jelaskan secara jelas mengapa laporan ini tidak valid (misal: di luar area sekolah, informasi fiktif, atau bukan kewenangan sekolah)..." class="w-full bg-zinc-900 border border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:border-red-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-800">
                    <button type="button" @click="modalReject = false" class="px-3 py-1.5 rounded-lg text-zinc-400 hover:text-zinc-200 text-xs transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-500 text-white font-medium text-xs shadow-sm transition">
                        Tolak & Arsipkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Petugas Buat Laporan Tindak Lanjut -->
    <div x-show="modalResolve" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-zinc-950 border border-zinc-800 rounded-xl p-5 max-w-lg w-full shadow-2xl space-y-4" @click.outside="modalResolve = false">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
                <div class="flex items-center gap-2 text-zinc-100 font-semibold text-sm">
                    <i data-lucide="send" class="w-4 h-4 text-orange-400"></i>
                    <span>Laporan Hasil Penanganan (Ke Kepala Sekolah)</span>
                </div>
                <button @click="modalResolve = false" class="text-zinc-500 hover:text-zinc-300 p-1"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>

            <form action="{{ route('dashboard.pengaduan.resolve', $complaint->ticket_code) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1.5">Uraian Tindakan / Perbaikan yang Telah Dilakukan *</label>
                    <textarea name="resolution_notes" rows="4" required placeholder="Tuliskan berita acara tindakan (misal: penggantian bohlam LED baru, mediasi kedua belah pihak dengan konseling, perbaikan engsel pintu)..." class="w-full bg-zinc-900 border border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:border-orange-500">{{ $complaint->resolution_notes }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1.5">Unggah Foto Bukti Hasil Penanganan (Opsional)</label>
                    <input type="file" name="resolution_attachments[]" multiple accept="image/*,.pdf" class="w-full bg-zinc-900 border border-zinc-800 rounded-lg p-2 text-xs text-zinc-300 file:mr-3 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:font-mono file:bg-zinc-800 file:text-zinc-200">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-800">
                    <button type="button" @click="modalResolve = false" class="px-3 py-1.5 rounded-lg text-zinc-400 hover:text-zinc-200 text-xs transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium text-xs shadow-sm transition">
                        Serahkan ke Kepala Sekolah
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: Kepala Sekolah Setujui Penutupan Kasus -->
    <div x-show="modalApprove" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-zinc-950 border border-zinc-800 rounded-xl p-5 max-w-lg w-full shadow-2xl space-y-4" @click.outside="modalApprove = false">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
                <div class="flex items-center gap-2 text-zinc-100 font-semibold text-sm">
                    <i data-lucide="award" class="w-4 h-4 text-emerald-400"></i>
                    <span>Persetujuan Penutupan Kasus</span>
                </div>
                <button @click="modalApprove = false" class="text-zinc-500 hover:text-zinc-300 p-1"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>

            <form action="{{ route('dashboard.pengaduan.approve', $complaint->ticket_code) }}" method="POST" class="space-y-4">
                @csrf
                <div class="p-3.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-300">
                    Menyetujui laporan ini akan mengubah status pengaduan menjadi <strong>Selesai</strong> dan menerbitkan notifikasi tuntas kepada siswa pelapor.
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1.5">Catatan / Arahan Kepala Sekolah (Opsional)</label>
                    <textarea name="approval_notes" rows="3" placeholder="Laporan hasil penanganan telah ditinjau dan disetujui..." class="w-full bg-zinc-900 border border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-800">
                    <button type="button" @click="modalApprove = false" class="px-3 py-1.5 rounded-lg text-zinc-400 hover:text-zinc-200 text-xs transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-xs shadow-sm transition">
                        Sahkan & Tutup Kasus
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5: Kepala Sekolah Minta Revisi -->
    <div x-show="modalRevision" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-zinc-950 border border-zinc-800 rounded-xl p-5 max-w-lg w-full shadow-2xl space-y-4" @click.outside="modalRevision = false">
            <div class="flex items-center justify-between pb-3 border-b border-zinc-800">
                <div class="flex items-center gap-2 text-zinc-100 font-semibold text-sm">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 text-orange-400"></i>
                    <span>Kembalikan untuk Revisi Penanganan</span>
                </div>
                <button @click="modalRevision = false" class="text-zinc-500 hover:text-zinc-300 p-1"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>

            <form action="{{ route('dashboard.pengaduan.revision', $complaint->ticket_code) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1.5">Instruksi Tindak Lanjut Ulang / Revisi *</label>
                    <textarea name="revision_notes" rows="4" required placeholder="Jelaskan bagian mana yang belum memuaskan atau perlu ditinjau kembali oleh petugas..." class="w-full bg-zinc-900 border border-zinc-800 rounded-lg p-2.5 text-xs text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:border-orange-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-800">
                    <button type="button" @click="modalRevision = false" class="px-3 py-1.5 rounded-lg text-zinc-400 hover:text-zinc-200 text-xs transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-medium text-xs shadow-sm transition">
                        Kirim Instruksi Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
