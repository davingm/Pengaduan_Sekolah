@extends('layouts.app')

@section('title', 'Status Pengaduan: ' . $complaint->ticket_code)

@section('content')
<div class="py-10 bg-black min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Breadcrumb & Back -->
        <div class="flex items-center justify-between">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Beranda
            </a>

            @if(Auth::check() && (Auth::user()->isStaff() || Auth::id() === $complaint->user_id))
                <a href="{{ route('dashboard.pengaduan.show', $complaint->ticket_code) }}" class="inline-flex items-center gap-1.5 bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 px-3.5 py-1.5 rounded-xl text-xs font-bold transition">
                    <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 text-blue-500"></i> Buka di Dashboard Internal &rarr;
                </a>
            @endif
        </div>

        <!-- Ticket Hero Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-sm font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 px-3 py-1 rounded-xl border border-blue-200 dark:border-blue-800">
                            {{ $complaint->ticket_code }}
                        </span>
                        <x-status-badge :status="$complaint->status" />
                        <x-priority-badge :priority="$complaint->priority" />
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white pt-1">
                        {{ $complaint->title }}
                    </h1>
                </div>

                <div class="text-left sm:text-right shrink-0 text-xs text-slate-500">
                    <div>Waktu Dibuat:</div>
                    <div class="font-semibold text-slate-700 dark:text-slate-300">{{ $complaint->created_at->format('d M Y, H:i') }} WIB</div>
                </div>
            </div>

            <!-- Workflow Progress Stepper (Visual 4-Steps Nuxt-style bar) -->
            <div class="py-2">
                <div class="grid grid-cols-4 gap-2 sm:gap-4 relative text-center">
                    
                    <!-- Step 1: Laporan Dibuat -->
                    <div class="flex flex-col items-center space-y-2">
                        <div class="w-9 h-9 rounded-2xl flex items-center justify-center text-xs font-bold transition shadow-sm bg-blue-600 text-white shadow-blue-500/20">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <div class="text-[11px] font-bold text-slate-800 dark:text-slate-200">1. Terkirim</div>
                        <span class="text-[10px] text-slate-400 hidden sm:block">Siswa / Pelapor</span>
                    </div>

                    <!-- Step 2: Verifikasi & Disposisi -->
                    @php $isStep2 = in_array($complaint->status, ['didisposisikan', 'diproses', 'menunggu_persetujuan', 'selesai']); @endphp
                    <div class="flex flex-col items-center space-y-2">
                        <div class="w-9 h-9 rounded-2xl flex items-center justify-center text-xs font-bold transition shadow-sm {{ $isStep2 ? 'bg-indigo-600 text-white shadow-indigo-500/20' : ($complaint->status === 'ditolak' ? 'bg-rose-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-400') }}">
                            @if($isStep2) <i data-lucide="check" class="w-4 h-4"></i>
                            @elseif($complaint->status === 'ditolak') <i data-lucide="x" class="w-4 h-4"></i>
                            @else 2 @endif
                        </div>
                        <div class="text-[11px] font-bold {{ $isStep2 ? 'text-slate-800 dark:text-slate-200' : 'text-slate-400' }}">2. Verifikasi Piket</div>
                        <span class="text-[10px] text-slate-400 hidden sm:block">Validasi & Disposisi</span>
                    </div>

                    <!-- Step 3: Tindak Lanjut Petugas -->
                    @php $isStep3 = in_array($complaint->status, ['diproses', 'menunggu_persetujuan', 'selesai']); @endphp
                    <div class="flex flex-col items-center space-y-2">
                        <div class="w-9 h-9 rounded-2xl flex items-center justify-center text-xs font-bold transition shadow-sm {{ $isStep3 ? 'bg-sky-600 text-white shadow-sky-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                            @if(in_array($complaint->status, ['menunggu_persetujuan', 'selesai'])) <i data-lucide="check" class="w-4 h-4"></i>
                            @elseif($complaint->status === 'diproses') <i data-lucide="loader" class="w-4 h-4 animate-spin"></i>
                            @else 3 @endif
                        </div>
                        <div class="text-[11px] font-bold {{ $isStep3 ? 'text-slate-800 dark:text-slate-200' : 'text-slate-400' }}">3. Penanganan</div>
                        <span class="text-[10px] text-slate-400 hidden sm:block">Petugas Terkait</span>
                    </div>

                    <!-- Step 4: Selesai & Pengesahan Kepsek -->
                    @php $isStep4 = $complaint->status === 'selesai'; @endphp
                    <div class="flex flex-col items-center space-y-2">
                        <div class="w-9 h-9 rounded-2xl flex items-center justify-center text-xs font-bold transition shadow-sm {{ $isStep4 ? 'bg-emerald-600 text-white shadow-emerald-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                            @if($isStep4) <i data-lucide="check" class="w-4 h-4"></i>
                            @else 4 @endif
                        </div>
                        <div class="text-[11px] font-bold {{ $isStep4 ? 'text-slate-800 dark:text-slate-200' : 'text-slate-400' }}">4. Selesai</div>
                        <span class="text-[10px] text-slate-400 hidden sm:block">Persetujuan Kepsek</span>
                    </div>

                </div>
            </div>

            <!-- Rejection Alert if Status Ditolak -->
            @if($complaint->status === 'ditolak')
                <div class="p-5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-xs text-rose-900 dark:text-rose-200 space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-rose-700 dark:text-rose-300">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i> Laporan Pengaduan Tidak Valid / Ditolak & Diarsipkan
                    </div>
                    <p class="leading-relaxed">{{ $complaint->rejection_reason }}</p>
                </div>
            @endif

            <!-- Details Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 text-xs">
                <div class="space-y-3 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kategori:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $complaint->category->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Lokasi / Ruangan:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $complaint->location ?? 'Tidak disebutkan' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Pelapor:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">
                            @if($complaint->is_anonymous)
                                <span class="text-amber-600 dark:text-amber-400 font-mono">Pelapor Anonim (Dirahasiakan)</span>
                            @else
                                {{ $complaint->reporter_name }} ({{ $complaint->reporter_class ?? 'Siswa' }})
                            @endif
                        </span>
                    </div>
                </div>

                <div class="space-y-3 bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Guru Verifikator:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $complaint->assignedByGuru->name ?? 'Menunggu Verifikasi Piket' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Petugas Penanganan:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $complaint->assignedOfficer->name ?? 'Belum Didisposisikan' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Terakhir Diperbarui:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $complaint->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2 pt-2">
                <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Uraian / Deskripsi Laporan:</h3>
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line">
                    {{ $complaint->description }}
                </div>
            </div>

            <!-- Evidence Attachments (Foto Bukti Awal) -->
            @if($complaint->evidenceAttachments->isNotEmpty())
                <div class="space-y-2 pt-2">
                    <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Foto / Dokumen Bukti Awal:</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach($complaint->evidenceAttachments as $att)
                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="block group relative rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
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

            <!-- Resolution Notes & Evidence (Hasil Penanganan Petugas & Approval Kepsek) -->
            @if($complaint->resolution_notes || $complaint->resolutionAttachments->isNotEmpty() || $complaint->approval_notes)
                <div class="mt-6 p-6 rounded-3xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 space-y-4">
                    <div class="flex items-center gap-2 text-emerald-800 dark:text-emerald-300 font-bold text-sm">
                        <i data-lucide="check-check" class="w-5 h-5"></i>
                        <span>Laporan Tindak Lanjut & Bukti Penyelesaian</span>
                    </div>

                    @if($complaint->resolution_notes)
                        <div class="text-xs text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line bg-white/70 dark:bg-slate-900/70 p-4 rounded-2xl border border-emerald-100 dark:border-emerald-900">
                            <span class="font-bold block text-emerald-700 dark:text-emerald-400 mb-1">Catatan Tindakan Petugas:</span>
                            {{ $complaint->resolution_notes }}
                        </div>
                    @endif

                    @if($complaint->approval_notes)
                        <div class="text-xs text-slate-800 dark:text-slate-200 leading-relaxed whitespace-pre-line bg-white/70 dark:bg-slate-900/70 p-4 rounded-2xl border border-emerald-100 dark:border-emerald-900">
                            <span class="font-bold block text-purple-700 dark:text-purple-400 mb-1">Catatan Pengesahan Kepala Sekolah:</span>
                            {{ $complaint->approval_notes }}
                        </div>
                    @endif

                    @if($complaint->resolutionAttachments->isNotEmpty())
                        <div class="space-y-1.5">
                            <div class="text-[11px] font-bold text-emerald-800 dark:text-emerald-300 uppercase">Foto Bukti Setelah Penanganan Selesai:</div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach($complaint->resolutionAttachments as $att)
                                    <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank" class="block group relative rounded-2xl overflow-hidden border border-emerald-200 dark:border-emerald-800 bg-white">
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

            <!-- Satisfaction Rating & Feedback Card (When Status Selesai) -->
            @if($complaint->status === 'selesai')
                <div class="p-6 rounded-3xl bg-amber-50/80 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/60 space-y-4"
                     x-data="{ rating: {{ $complaint->satisfaction_rating ?? 5 }} }">
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-amber-800 dark:text-amber-300 font-bold text-sm">
                            <i data-lucide="star" class="w-5 h-5 text-amber-500 fill-amber-500"></i>
                            <span>Penilaian Kepuasan Siswa / Pelapor</span>
                        </div>
                        @if($complaint->satisfaction_rating)
                            <span class="text-xs font-bold text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/60 px-3 py-1 rounded-full">
                                Nilai: {{ $complaint->satisfaction_rating }} / 5 Bintang
                            </span>
                        @endif
                    </div>

                    @if($complaint->satisfaction_rating)
                        <div class="text-xs text-slate-700 dark:text-slate-300 italic bg-white/70 dark:bg-slate-900/70 p-4 rounded-2xl border border-amber-100 dark:border-amber-900">
                            "{{ $complaint->satisfaction_feedback ?? 'Pelapor puas dengan kecepatan penanganan.' }}"
                            <div class="text-[10px] text-slate-400 mt-1 not-italic font-sans">
                                Dinilai pada {{ $complaint->feedback_submitted_at?->format('d M Y, H:i') }} WIB
                            </div>
                        </div>
                    @else
                        <!-- Form Submit Rating -->
                        <form action="{{ route('pengaduan.rate', $complaint->ticket_code) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                                    Seberapa puas Anda dengan hasil penanganan masalah ini?
                                </label>
                                <div class="flex items-center gap-2">
                                    <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                        <button type="button" 
                                                @click="rating = star" 
                                                class="p-2 rounded-xl border transition"
                                                :class="rating >= star ? 'bg-amber-400 text-slate-950 border-amber-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 border-slate-200 dark:border-slate-700'">
                                            <i data-lucide="star" class="w-5 h-5" :class="rating >= star ? 'fill-current' : ''"></i>
                                        </button>
                                    </template>
                                    <input type="hidden" name="satisfaction_rating" :value="rating">
                                    <span class="text-xs font-bold text-amber-700 dark:text-amber-400 ml-2" x-text="rating + ' / 5 Bintang'"></span>
                                </div>
                            </div>

                            <div>
                                <textarea name="satisfaction_feedback" rows="2" placeholder="Tuliskan ulasan singkat atau terima kasih untuk petugas..." class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
                            </div>

                            <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-5 py-2.5 rounded-xl text-xs transition">
                                Kirimkan Ulasan Kepuasan
                            </button>
                        </form>
                    @endif
                </div>
            @endif

        </div>

        <!-- Section 2: Timeline Aktivitas (Audit Trail) & Tanya Jawab -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left: Timeline Logs -->
            <div class="lg:col-span-6 space-y-4">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-lg">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2 mb-6">
                        <i data-lucide="history" class="w-4 h-4 text-blue-500"></i>
                        <span>Riwayat / Audit Trail Pengaduan</span>
                    </h3>

                    <div class="space-y-6 relative before:absolute before:inset-0 before:left-3.5 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
                        @foreach($complaint->logs as $log)
                            <div class="relative flex items-start gap-4">
                                <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold z-10 shrink-0 shadow-md">
                                    <i data-lucide="circle-dot" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="flex-1 bg-slate-50 dark:bg-slate-800/50 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800 text-xs">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-bold text-slate-900 dark:text-white">{{ $log->actor_name }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $log->created_at->format('d M, H:i') }}</span>
                                    </div>
                                    <div class="text-slate-600 dark:text-slate-300 leading-relaxed">{{ $log->notes }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Discussion / Responses -->
            <div class="lg:col-span-6 space-y-4">
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-lg space-y-4">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <i data-lucide="message-square" class="w-4 h-4 text-blue-500"></i>
                        <span>Pesan & Klarifikasi Tambahan</span>
                    </h3>

                    <!-- Message Feed -->
                    <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                        @forelse($complaint->responses->where('is_internal', false) as $res)
                            <div class="p-3.5 rounded-2xl text-xs {{ $res->user_id === $complaint->user_id ? 'bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800' : 'bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                        <span>{{ $res->sender_name }}</span>
                                        <span class="text-[10px] font-normal text-slate-400">({{ $res->sender_role }})</span>
                                    </span>
                                    <span class="text-[10px] text-slate-400">{{ $res->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 leading-relaxed">{{ $res->message }}</p>
                            </div>
                        @empty
                            <div class="text-center py-6 text-xs text-slate-400">
                                Belum ada tanggapan atau pesan klarifikasi tambahan.
                            </div>
                        @endforelse
                    </div>

                    <!-- Send Message Form -->
                    <form action="{{ route('pengaduan.response', $complaint->ticket_code) }}" method="POST" class="space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                        @csrf
                        @if(!Auth::check())
                            <input type="text" name="sender_name" placeholder="Nama Anda (Pelapor)" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none">
                        @endif
                        <textarea name="message" rows="2" required placeholder="Tuliskan pesan atau klarifikasi tambahan..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                        <button type="submit" class="w-full flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 rounded-xl text-xs transition">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                            <span>Kirim Pesan</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
