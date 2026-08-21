@extends('layouts.dashboard')

@section('title', 'Laporan & Rekapitulasi')
@section('page_title', 'Laporan & Rekapitulasi Pengaduan')
@section('page_description', 'Analisis data performa penanganan pengaduan dan cetak rekapitulasi resmi.')

@section('content')
<div class="space-y-6">
    
    <!-- Filter Date Range & Parameters -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-sm text-white flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4 text-blue-400"></i>
                <span>Filter Parameter Laporan</span>
            </h3>

            <!-- Cetak Laporan Button -->
            <a href="{{ route('dashboard.laporan.print', request()->query()) }}" target="_blank" class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-lg shadow-emerald-600/30 transition">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Cetak / Ekspor PDF</span>
            </a>
        </div>

        <form action="{{ route('dashboard.laporan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5">Status Pengaduan</label>
                <select name="status" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="menunggu_verifikasi" {{ $status === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="diproses" {{ $status === 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="selesai" {{ $status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 rounded-xl text-xs transition">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Stats Metrics -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 text-center">
            <div class="text-xs text-slate-400">Total Masuk</div>
            <div class="text-2xl font-black text-white mt-1">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 text-center">
            <div class="text-xs text-amber-400">Menunggu</div>
            <div class="text-2xl font-black text-amber-400 mt-1">{{ $stats['menunggu'] }}</div>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 text-center">
            <div class="text-xs text-sky-400">Diproses</div>
            <div class="text-2xl font-black text-sky-400 mt-1">{{ $stats['diproses'] }}</div>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 text-center">
            <div class="text-xs text-emerald-400">Selesai</div>
            <div class="text-2xl font-black text-emerald-400 mt-1">{{ $stats['selesai'] }}</div>
        </div>
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 text-center">
            <div class="text-xs text-purple-400">Rata-rata Rating</div>
            <div class="text-2xl font-black text-purple-400 mt-1">★ {{ $stats['rating_avg'] }}/5</div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/60 border-b border-slate-800 text-slate-400 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-4">Kode Tiket</th>
                        <th class="px-5 py-4">Tanggal</th>
                        <th class="px-5 py-4">Pelapor</th>
                        <th class="px-5 py-4">Kategori & Judul</th>
                        <th class="px-5 py-4">Petugas</th>
                        <th class="px-5 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($complaints as $c)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-5 py-3 font-mono font-bold text-blue-400">{{ $c->ticket_code }}</td>
                            <td class="px-5 py-3 text-slate-400">{{ $c->created_at->format('d/m/Y') }}</td>
                            <td class="px-5 py-3">{{ $c->is_anonymous ? 'Anonim' : $c->reporter_name }}</td>
                            <td class="px-5 py-3">
                                <div class="font-bold text-white">{{ $c->title }}</div>
                                <div class="text-[10px] text-slate-400">{{ $c->category->name }}</div>
                            </td>
                            <td class="px-5 py-3 text-slate-400">{{ $c->assignedOfficer->name ?? '-' }}</td>
                            <td class="px-5 py-3"><x-status-badge :status="$c->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-500">Tidak ada data untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
