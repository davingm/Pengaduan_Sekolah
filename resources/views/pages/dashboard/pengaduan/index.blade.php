@extends('layouts.dashboard')

@section('title', 'Daftar Pengaduan')
@section('page_title', 'Daftar Pengaduan')
@section('page_description', 'Manajemen status, disposisi, dan riwayat penanganan seluruh tiket pengaduan.')

@section('content')
@php
    $currentStatus = $currentStatus ?? request()->get('status', 'all');
    $tabs = $tabs ?? [
        'all' => ['label' => 'Semua', 'count' => $complaints->total() ?? count($complaints)],
        'menunggu_verifikasi' => ['label' => 'Menunggu Verifikasi', 'count' => $statusCounts['menunggu_verifikasi'] ?? 0],
        'didisposisikan' => ['label' => 'Didisposisikan', 'count' => $statusCounts['didisposisikan'] ?? 0],
        'diproses' => ['label' => 'Sedang Ditangani', 'count' => $statusCounts['diproses'] ?? 0],
        'menunggu_persetujuan' => ['label' => 'Menunggu Persetujuan', 'count' => $statusCounts['menunggu_persetujuan'] ?? 0],
        'selesai' => ['label' => 'Selesai', 'count' => $statusCounts['selesai'] ?? 0],
        'ditolak' => ['label' => 'Ditolak', 'count' => $statusCounts['ditolak'] ?? 0],
    ];
@endphp

<div class="space-y-5">
    
    <!-- Status Filter Tabs (Sheaf UI Segmented Filter) -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 border-b border-zinc-800/80 scrollbar-none">
        @foreach($tabs as $key => $tab)
            <a href="{{ route('dashboard.pengaduan.index', array_merge(request()->except('page'), ['status' => $key])) }}" 
               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-mono font-medium transition-all shrink-0 {{ $currentStatus === $key ? 'bg-zinc-900 text-orange-400 border border-zinc-800 shadow-xs' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900/50 border border-transparent' }}">
                <span>{{ $tab['label'] }}</span>
                <span class="text-[10px] px-1.5 py-0.2 rounded font-mono {{ $currentStatus === $key ? 'bg-orange-500/20 text-orange-300' : 'bg-zinc-800 text-zinc-400' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Quick Search & Category Filter Bar (Teacher-Friendly UX) -->
    <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl p-3.5 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <form action="{{ route('dashboard.pengaduan.index') }}" method="GET" class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <input type="hidden" name="status" value="{{ $currentStatus }}">
            
            <div class="relative flex-1">
                <i data-lucide="search" class="w-3.5 h-3.5 text-zinc-500 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}" 
                       placeholder="Cari kode tiket, nama siswa, atau kata kunci masalah..." 
                       class="w-full bg-zinc-900 border border-zinc-800 rounded-lg pl-9 pr-3 py-1.5 text-xs text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:border-orange-500">
            </div>

            @if(isset($categories) && count($categories) > 0)
                <select name="category_id" 
                        class="bg-zinc-900 border border-zinc-800 rounded-lg px-3 py-1.5 text-xs text-zinc-300 focus:outline-none focus:border-orange-500">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="px-3.5 py-1.5 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-700 text-xs font-medium transition-all">
                    Cari
                </button>
                @if(request()->has('q') || request()->has('category_id'))
                    <a href="{{ route('dashboard.pengaduan.index', ['status' => $currentStatus]) }}" 
                       class="px-2.5 py-1.5 rounded-lg text-zinc-500 hover:text-zinc-300 text-xs font-mono transition-colors" title="Reset Pencarian">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="text-[11px] font-mono text-zinc-500 text-right shrink-0">
            Menampilkan <span class="text-zinc-300 font-semibold">{{ $complaints->count() }}</span> data
        </div>
    </div>

    <!-- Data Table (Sheaf UI / shadcn Data Table) -->
    <div class="bg-zinc-950/60 border border-zinc-800/80 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-zinc-900/50 border-b border-zinc-800/80 text-zinc-400 font-mono text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Kode Tiket</th>
                        <th class="px-4 py-3 font-semibold">Judul & Masalah</th>
                        <th class="px-4 py-3 font-semibold">Pelapor</th>
                        <th class="px-4 py-3 font-semibold">Urgensi</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Waktu Masuk</th>
                        <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60 text-zinc-300">
                    @forelse($complaints as $c)
                        <tr class="hover:bg-zinc-900/40 transition-colors group">
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-mono text-xs font-semibold text-zinc-300 bg-zinc-900 px-2 py-0.5 rounded border border-zinc-800">
                                    {{ $c->ticket_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 min-w-[240px]">
                                <div class="font-medium text-zinc-100 group-hover:text-orange-400 transition-colors truncate max-w-md">
                                    {{ $c->title }}
                                </div>
                                <div class="text-[11px] font-mono text-zinc-500 mt-0.5 flex items-center gap-1.5">
                                    <span>{{ $c->category->name }}</span>
                                    @if($c->location)
                                        <span>&bull;</span>
                                        <span class="truncate">{{ $c->location }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="text-zinc-300 font-medium">
                                    {{ $c->is_anonymous ? 'Siswa Anonim' : $c->reporter_name }}
                                </span>
                                @if($c->reporter_class)
                                    <div class="text-[10px] font-mono text-zinc-500">{{ $c->reporter_class }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <x-priority-badge :priority="$c->priority" />
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <x-status-badge :status="$c->status" />
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap font-mono text-[11px] text-zinc-400">
                                {{ $c->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <a href="{{ route('dashboard.pengaduan.show', $c->ticket_code) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-900 hover:bg-orange-500 hover:text-white text-zinc-200 border border-zinc-800 font-medium text-xs transition-all shadow-xs">
                                    <span>Kelola</span>
                                    <i data-lucide="chevron-right" class="w-3 h-3 text-zinc-500 group-hover:text-white"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-zinc-600 font-mono text-xs">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <i data-lucide="inbox" class="w-8 h-8 text-zinc-700"></i>
                                    <span>Tidak ada pengaduan yang cocok dengan filter / pencarian ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($complaints, 'links') && $complaints->hasPages())
            <div class="p-4 border-t border-zinc-800/80 bg-zinc-950">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>

</div>
@endsection