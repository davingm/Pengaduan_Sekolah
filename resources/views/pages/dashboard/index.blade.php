@extends('layouts.dashboard')

@section('title', 'Dashboard - ' . Auth::user()->role_label)
@section('page_title', 'Ringkasan Dashboard')
@section('page_description', 'Selamat datang kembali, ' . Auth::user()->name)

@section('content')
<div class="space-y-8">
    
    <!-- Top Role Welcome Card -->
    <div class="bg-gradient-to-r from-blue-900/40 via-indigo-900/30 to-purple-900/40 border border-blue-500/20 rounded-3xl p-6 sm:p-8 backdrop-blur-xl relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative z-10">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold border {{ Auth::user()->role_badge }}">
                    <span>Mode Akses:</span>
                    <span>{{ Auth::user()->role_label }}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                    Halo, {{ Auth::user()->name }}! 👋
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 max-w-xl">
                    @if(Auth::user()->isSiswa())
                        Pantau status pengaduan fasilitas atau aspirasi yang Anda ajukan di sini.
                    @elseif(Auth::user()->isGuruPiket())
                        Anda bertindak sebagai <strong>Verifikator Awal</strong>. Periksa pengaduan baru yang masuk, validasi atau tolak laporan tidak layak, dan disposisikan ke petugas terkait.
                    @elseif(Auth::user()->isPetugas())
                        Anda bertindak sebagai <strong>Petugas Penanganan Lapangan</strong>. Selesaikan tugas perbaikan, mediasi, dan serahkan laporan tindak lanjut ke Kepala Sekolah.
                    @elseif(Auth::user()->isKepalaSekolah())
                        Anda bertindak sebagai <strong>Pimpinan & Pengesah Akhir</strong>. Tinjau laporan hasil penanganan petugas dan berikan persetujuan penutupan kasus.
                    @else
                        Kontrol seluruh alur master data sistem pengaduan sekolah.
                    @endif
                </p>
            </div>

            @if(Auth::user()->isSiswa())
                <a href="{{ route('pengaduan.create') }}" class="shrink-0 flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-bold px-5 py-3 rounded-2xl shadow-lg shadow-blue-600/30 transition text-xs">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Buat Pengaduan Baru</span>
                </a>
            @else
                <a href="{{ route('dashboard.pengaduan.index') }}" class="shrink-0 flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold px-4 py-2.5 rounded-2xl border border-slate-700 transition text-xs">
                    <i data-lucide="inbox" class="w-4 h-4 text-blue-400"></i>
                    <span>Buka Semua Pengaduan</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400">Total Pengaduan</span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center">
                    <i data-lucide="folder" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-white">{{ $stats['total'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Seluruh data tercatat</div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400">
                    {{ Auth::user()->isSiswa() ? 'Menunggu Verifikasi' : 'Antrean Guru Piket' }}
                </span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-amber-400">
                {{ Auth::user()->isSiswa() ? $stats['menunggu'] : $stats['menunggu_verifikasi'] }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1">Perlu dicek & divalidasi</div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400">
                    {{ Auth::user()->isPetugas() ? 'Tugas Saya' : 'Sedang Ditangani' }}
                </span>
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center">
                    <i data-lucide="loader-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-sky-400">
                {{ Auth::user()->isPetugas() ? $stats['tugas_saya'] : $stats['diproses'] }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1">Proses tindakan lapangan</div>
        </div>

        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-400">Kasus Selesai</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-emerald-400">{{ $stats['selesai'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Telah disetujui & tuntas</div>
        </div>

    </div>

    <!-- Role-Specific Actionable Queue or My Complaints -->
    @if(Auth::user()->isSiswa())
        <!-- Siswa: My Complaints List -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-white">Riwayat Pengaduan Saya</h3>
                    <p class="text-xs text-slate-400">Daftar aspirasi yang Anda kirimkan ke sekolah</p>
                </div>
                <a href="{{ route('pengaduan.create') }}" class="text-xs font-bold text-blue-400 hover:underline">
                    + Buat Laporan Baru
                </a>
            </div>

            <div class="divide-y divide-slate-800">
                @forelse($myComplaints as $c)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-800/40 p-2 rounded-2xl transition">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-blue-400 bg-blue-950/60 px-2 py-0.5 rounded border border-blue-800">
                                    {{ $c->ticket_code }}
                                </span>
                                <x-status-badge :status="$c->status" />
                                <span class="text-xs text-slate-400">&bull; {{ $c->category->name }}</span>
                            </div>
                            <h4 class="font-bold text-sm text-white">{{ $c->title }}</h4>
                            <div class="text-xs text-slate-400">{{ $c->created_at->format('d M Y, H:i') }} WIB &bull; Lokasi: {{ $c->location ?? '-' }}</div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('pengaduan.show', $c->ticket_code) }}" class="px-3 py-1.5 rounded-xl bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 text-xs font-semibold border border-blue-500/30 transition">
                                Lacak Progres &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-500 text-xs">
                        Anda belum pernah mengirimkan pengaduan.
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <!-- Staff / Piket / Petugas / Kepsek: Priority Action Queue -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-white">
                        @if(Auth::user()->isGuruPiket())
                            Antrean Pengaduan Menunggu Verifikasi Anda
                        @elseif(Auth::user()->isPetugas())
                            Tugas Penanganan Lapangan yang Ditugaskan ke Anda
                        @elseif(Auth::user()->isKepalaSekolah())
                            Laporan Penanganan yang Menunggu Persetujuan Anda
                        @else
                            Pengaduan Prioritas Terkini
                        @endif
                    </h3>
                    <p class="text-xs text-slate-400">Klik pengaduan untuk membuka tindakan cepat</p>
                </div>
                <a href="{{ route('dashboard.pengaduan.index') }}" class="text-xs font-bold text-blue-400 hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-800">
                @forelse($priorityTasks as $item)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-800/40 p-2 rounded-2xl transition">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-blue-400 bg-blue-950/60 px-2 py-0.5 rounded border border-blue-800">
                                    {{ $item->ticket_code }}
                                </span>
                                <x-status-badge :status="$item->status" />
                                <x-priority-badge :priority="$item->priority" />
                                <span class="text-xs text-slate-400 hidden sm:inline">&bull; {{ $item->category->name }}</span>
                            </div>
                            <h4 class="font-bold text-sm text-white">{{ $item->title }}</h4>
                            <div class="text-xs text-slate-400">
                                Pelapor: <strong class="text-slate-300">{{ $item->is_anonymous ? 'Siswa Anonim' : $item->reporter_name }}</strong> 
                                &bull; Dibuat: {{ $item->created_at->diffForHumans() }}
                                @if($item->location) &bull; Lokasi: {{ $item->location }} @endif
                            </div>
                        </div>

                        <a href="{{ route('dashboard.pengaduan.show', $item->ticket_code) }}" class="shrink-0 inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-md shadow-blue-600/20 transition">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            <span>Buka Tindakan</span>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-500 text-xs">
                        Tidak ada antrean tugas yang memerlukan tindakan saat ini.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

</div>
@endsection
