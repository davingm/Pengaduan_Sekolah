@extends('layouts.dashboard')

@section('title', 'Dashboard - ' . Auth::user()->role_label)
@section('page_title', 'Ringkasan Dashboard')
@section('page_description', 'Selamat datang kembali, ' . Auth::user()->name)

@section('content')
<div class="space-y-6">
    
    <!-- Top Welcome Card (Sheaf UI Minimalist) -->
    <div class="bg-zinc-950/60 border border-zinc-800/90 rounded-xl p-5 sm:p-6 backdrop-blur-sm relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 relative z-10">
            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-mono font-medium bg-zinc-900 border border-zinc-800 text-zinc-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                    <span>{{ Auth::user()->role_label }}</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-semibold text-zinc-100 tracking-tight">
                    Halo, {{ Auth::user()->name }}
                </h2>
                <p class="text-xs text-zinc-400 max-w-2xl leading-relaxed">
                    @if(Auth::user()->isSiswa())
                        Pantau status penanganan laporan fasilitas atau aspirasi sekolah yang Anda ajukan.
                    @elseif(Auth::user()->isGuruPiket())
                        Anda bertindak sebagai <strong>Verifikator Awal</strong>. Validasi pengaduan masuk dan disposisikan ke unit penanganan teknis.
                    @elseif(Auth::user()->isPetugas())
                        Anda bertindak sebagai <strong>Petugas Lapangan</strong>. Selesaikan perbaikan fisik atau mediasi kasus dan kirimkan laporan penyelesaian.
                    @elseif(Auth::user()->isKepalaSekolah())
                        Anda bertindak sebagai <strong>Pimpinan & Pengesah Akhir</strong>. Tinjau hasil tindakan petugas dan sahkan penutupan kasus.
                    @else
                        Kontrol seluruh alur manajemen tiket, kategori, dan rekapitulasi data pengaduan sekolah.
                    @endif
                </p>
            </div>

            <div class="shrink-0 flex items-center gap-2">
                @if(Auth::user()->isSiswa())
                    <a href="{{ route('pengaduan.create') }}" 
                       class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-medium px-4 py-2 rounded-lg text-xs shadow-sm transition-all">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Buat Pengaduan</span>
                    </a>
                @else
                    <a href="{{ route('dashboard.pengaduan.index') }}" 
                       class="inline-flex items-center gap-2 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 font-medium px-3.5 py-2 rounded-lg text-xs transition-all">
                        <i data-lucide="inbox" class="w-4 h-4 text-orange-400"></i>
                        <span>Buka Semua Pengaduan</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Metric Cards Grid (Sheaf UI / Cloudflare Stat Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
        
        <!-- Total -->
        <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-4 sm:p-5 hover:border-zinc-700/80 transition-colors">
            <div class="flex items-center justify-between text-zinc-400 mb-2">
                <span class="text-xs font-medium text-zinc-400">Total Pengaduan</span>
                <i data-lucide="folder" class="w-4 h-4 text-zinc-500"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-mono font-semibold text-zinc-100">{{ $stats['total'] }}</div>
            <div class="text-[11px] font-mono text-zinc-500 mt-1">Seluruh data tercatat</div>
        </div>

        <!-- Menunggu -->
        <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-4 sm:p-5 hover:border-zinc-700/80 transition-colors">
            <div class="flex items-center justify-between text-zinc-400 mb-2">
                <span class="text-xs font-medium text-zinc-400">
                    {{ Auth::user()->isSiswa() ? 'Menunggu Respon' : 'Antrean Verifikasi' }}
                </span>
                <i data-lucide="clock" class="w-4 h-4 text-amber-400/80"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-mono font-semibold text-amber-400">
                {{ Auth::user()->isSiswa() ? $stats['menunggu'] : $stats['menunggu_verifikasi'] }}
            </div>
            <div class="text-[11px] font-mono text-zinc-500 mt-1">Perlu tindakan awal</div>
        </div>

        <!-- Diproses / Tugas Saya -->
        <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-4 sm:p-5 hover:border-zinc-700/80 transition-colors">
            <div class="flex items-center justify-between text-zinc-400 mb-2">
                <span class="text-xs font-medium text-zinc-400">
                    {{ Auth::user()->isPetugas() ? 'Tugas Saya' : 'Sedang Ditangani' }}
                </span>
                <i data-lucide="loader-2" class="w-4 h-4 text-orange-400/80"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-mono font-semibold text-orange-400">
                {{ Auth::user()->isPetugas() ? $stats['tugas_saya'] : $stats['diproses'] }}
            </div>
            <div class="text-[11px] font-mono text-zinc-500 mt-1">Proses penanganan aktif</div>
        </div>

        <!-- Selesai -->
        <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-4 sm:p-5 hover:border-zinc-700/80 transition-colors">
            <div class="flex items-center justify-between text-zinc-400 mb-2">
                <span class="text-xs font-medium text-zinc-400">Kasus Selesai</span>
                <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400/80"></i>
            </div>
            <div class="text-2xl sm:text-3xl font-mono font-semibold text-emerald-400">{{ $stats['selesai'] }}</div>
            <div class="text-[11px] font-mono text-zinc-500 mt-1">Telah diverifikasi & sah</div>
        </div>

    </div>

    <!-- Actionable Queue or My Complaints (Sheaf UI Data Card) -->
    @if(Auth::user()->isSiswa())
        <!-- Siswa: My Complaints List -->
        <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-800/80 pb-4">
                <div>
                    <h3 class="font-semibold text-sm text-zinc-100">Riwayat Pengaduan Saya</h3>
                    <p class="text-xs text-zinc-500">Daftar laporan yang pernah Anda kirimkan</p>
                </div>
                <a href="{{ route('pengaduan.create') }}" class="text-xs font-mono font-medium text-orange-400 hover:text-orange-300 transition-colors">
                    + Buat Laporan &rarr;
                </a>
            </div>

            <div class="divide-y divide-zinc-800/60">
                @forelse($myComplaints as $c)
                    <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-zinc-900/40 p-2.5 rounded-lg transition-colors">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-xs font-semibold text-zinc-300 bg-zinc-900 px-2 py-0.5 rounded border border-zinc-800">
                                    {{ $c->ticket_code }}
                                </span>
                                <x-status-badge :status="$c->status" />
                                <span class="text-xs text-zinc-500 font-mono">&bull; {{ $c->category->name }}</span>
                            </div>
                            <h4 class="font-medium text-sm text-zinc-200">{{ $c->title }}</h4>
                            <div class="text-[11px] text-zinc-500 font-mono">
                                {{ $c->created_at->format('d M Y, H:i') }} WIB 
                                @if($c->location) &bull; Lokasi: {{ $c->location }} @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('pengaduan.show', $c->ticket_code) }}" 
                               class="px-3 py-1.5 rounded-md bg-zinc-900 hover:bg-zinc-800 text-zinc-300 hover:text-zinc-100 text-xs font-mono border border-zinc-800 transition-colors">
                                Lacak Progres &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-zinc-600 text-xs font-mono">
                        Anda belum pernah mengirimkan pengaduan.
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <!-- Staff / Piket / Petugas / Kepsek: Priority Action Queue -->
        <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-zinc-800/80 pb-4">
                <div>
                    <h3 class="font-semibold text-sm text-zinc-100">
                        @if(Auth::user()->isGuruPiket())
                            Antrean Pengaduan Menunggu Verifikasi Anda
                        @elseif(Auth::user()->isPetugas())
                            Tugas Penanganan yang Ditugaskan ke Anda
                        @elseif(Auth::user()->isKepalaSekolah())
                            Laporan Hasil Tindakan Menunggu Persetujuan
                        @else
                            Pengaduan Prioritas Terkini
                        @endif
                    </h3>
                    <p class="text-xs text-zinc-500">Klik pengaduan untuk membuka tindakan lanjutan</p>
                </div>
                <a href="{{ route('dashboard.pengaduan.index') }}" class="text-xs font-mono font-medium text-orange-400 hover:text-orange-300 transition-colors">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-zinc-800/60">
                @forelse($priorityTasks as $item)
                    <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-zinc-900/40 p-2.5 rounded-lg transition-colors">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-xs font-semibold text-zinc-300 bg-zinc-900 px-2 py-0.5 rounded border border-zinc-800">
                                    {{ $item->ticket_code }}
                                </span>
                                <x-status-badge :status="$item->status" />
                                <x-priority-badge :priority="$item->priority" />
                                <span class="text-xs text-zinc-500 font-mono hidden sm:inline">&bull; {{ $item->category->name }}</span>
                            </div>
                            <h4 class="font-medium text-sm text-zinc-200">{{ $item->title }}</h4>
                            <div class="text-[11px] text-zinc-500 font-mono">
                                Pelapor: <span class="text-zinc-400">{{ $item->is_anonymous ? 'Siswa Anonim' : $item->reporter_name }}</span> 
                                &bull; Dibuat: {{ $item->created_at->diffForHumans() }}
                                @if($item->location) &bull; Lokasi: {{ $item->location }} @endif
                            </div>
                        </div>

                        <a href="{{ route('dashboard.pengaduan.show', $item->ticket_code) }}" 
                           class="shrink-0 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium shadow-xs transition-all">
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            <span>Buka Tindakan</span>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-12 text-zinc-600 text-xs font-mono">
                        Tidak ada antrean tugas yang memerlukan tindakan saat ini.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

</div>
@endsection
