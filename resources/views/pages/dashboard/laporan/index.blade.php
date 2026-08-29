@extends('layouts.dashboard')

@section('title', 'Laporan & Rekapitulasi')
@section('page_title', 'Laporan & Rekapitulasi')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Date Range & Parameters -->
    <div class="cf-card p-5 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-zinc-800/80">
            <h3 class="font-semibold text-xs text-zinc-200 flex items-center gap-2 font-sans">
                <i data-lucide="filter" class="w-3.5 h-3.5 text-zinc-400"></i>
                <span>Filter Parameter Laporan</span>
            </h3>

            <!-- Cetak Laporan Button -->
            <a href="{{ route('dashboard.laporan.print', request()->query()) }}" target="_blank" 
               class="inline-flex items-center gap-1.5 bg-zinc-100 hover:bg-white text-zinc-950 text-xs font-medium px-3.5 py-1.5 rounded-md shadow-xs transition-all">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Cetak PDF</span>
            </a>
        </div>

        <form action="{{ route('dashboard.laporan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-mono font-medium text-zinc-400 mb-1">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="w-full bg-[#121215] border border-zinc-800 rounded-md px-3 py-1.5 text-xs text-zinc-100 focus:outline-none focus:border-zinc-500 font-mono">
            </div>

            <div>
                <label class="block text-[11px] font-mono font-medium text-zinc-400 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="w-full bg-[#121215] border border-zinc-800 rounded-md px-3 py-1.5 text-xs text-zinc-100 focus:outline-none focus:border-zinc-500 font-mono">
            </div>

            <div>
                <label class="block text-[11px] font-mono font-medium text-zinc-400 mb-1">Status Pengaduan</label>
                <select name="status" class="w-full bg-[#121215] border border-zinc-800 rounded-md px-3 py-1.5 text-xs text-zinc-100 focus:outline-none focus:border-zinc-500 font-mono">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="menunggu_verifikasi" {{ $status === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="diproses" {{ $status === 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" 
                        class="w-full bg-[#18181b] hover:bg-[#27272a] text-zinc-100 border border-zinc-700/80 font-medium py-1.5 rounded-md text-xs transition-all">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Stats Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="cf-card p-3.5 text-center">
            <div class="text-[11px] font-mono text-zinc-400">Total Masuk</div>
            <div class="text-xl font-mono font-semibold text-zinc-100 mt-1">{{ $stats['total'] }}</div>
        </div>
        <div class="cf-card p-3.5 text-center">
            <div class="text-[11px] font-mono text-zinc-400">Menunggu</div>
            <div class="text-xl font-mono font-semibold text-zinc-200 mt-1">{{ $stats['menunggu'] }}</div>
        </div>
        <div class="cf-card p-3.5 text-center">
            <div class="text-[11px] font-mono text-zinc-400">Diproses</div>
            <div class="text-xl font-mono font-semibold text-zinc-200 mt-1">{{ $stats['diproses'] }}</div>
        </div>
        <div class="cf-card p-3.5 text-center">
            <div class="text-[11px] font-mono text-zinc-400">Selesai</div>
            <div class="text-xl font-mono font-semibold text-emerald-400 mt-1">{{ $stats['selesai'] }}</div>
        </div>
        <div class="cf-card p-3.5 text-center">
            <div class="text-[11px] font-mono text-zinc-400">Rating Rata-rata</div>
            <div class="text-xl font-mono font-semibold text-zinc-100 mt-1">★ {{ $stats['rating_avg'] }}/5</div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="cf-card overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#09090b] border-b border-zinc-800/80 text-zinc-400 font-mono text-[11px] uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Kode Tiket</th>
                        <th class="px-4 py-3 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 font-semibold">Pelapor</th>
                        <th class="px-4 py-3 font-semibold">Kategori & Judul</th>
                        <th class="px-4 py-3 font-semibold">Petugas</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800/60 text-zinc-300">
                    @forelse($complaints as $c)
                        <tr class="hover:bg-zinc-900/40 transition-colors">
                            <td class="px-4 py-3 font-mono font-semibold text-zinc-300">
                                <span class="bg-[#121215] px-2 py-0.5 rounded border border-zinc-800">
                                    {{ $c->ticket_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono text-zinc-400">{{ $c->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $c->is_anonymous ? 'Siswa Anonim' : $c->reporter_name }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-zinc-100">{{ $c->title }}</div>
                                <div class="text-[10px] font-mono text-zinc-500">{{ $c->category->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-zinc-400 font-mono">{{ $c->assignedOfficer->name ?? '-' }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$c->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-zinc-600 font-mono text-xs">
                                Tidak ada data pengaduan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
