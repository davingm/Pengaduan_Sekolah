@extends('layouts.dashboard')

@section('title', 'Daftar Pengaduan')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12">
    
    <!-- Header: Bold & Raw -->
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold tracking-tighter text-white uppercase italic">
            MANAJEMEN<br>PENGADUAN
        </h1>
    </div>

    <!-- Filter Bar: Menggunakan gaya teks sederhana -->
    <div class="flex items-center gap-6 mb-10 overflow-x-auto border-y border-white/10 py-4">
        @foreach($tabs as $key => $tab)
            <a href="{{ route('dashboard.pengaduan.index', array_merge(request()->query(), ['status' => $key])) }}" 
               class="text-xs uppercase tracking-widest font-bold transition-all {{ $currentStatus === $key ? 'text-white' : 'text-zinc-600 hover:text-white' }}">
                {{ $tab['label'] }} <span class="ml-1 opacity-50">{{ $tab['count'] }}</span>
            </a>
        @endforeach
    </div>

    <!-- Data Table: Brutalist Style (Tanpa Rounded, Tanpa Shadow) -->
    <div class="w-full">
        <div class="grid grid-cols-6 border-b border-white/10 pb-4 mb-2 text-[10px] uppercase tracking-widest text-zinc-500 font-bold">
            <div class="col-span-1">Tiket</div>
            <div class="col-span-2">Judul</div>
            <div class="col-span-1">Pelapor</div>
            <div class="col-span-1">Status</div>
            <div class="col-span-1 text-right">Aksi</div>
        </div>

        <div class="flex flex-col">
            @forelse($complaints as $c)
                <div class="grid grid-cols-6 items-center py-6 border-b border-white/5 hover:bg-white/5 transition-colors group">
                    <div class="col-span-1 font-mono text-zinc-400 text-xs">{{ $c->ticket_code }}</div>
                    <div class="col-span-2">
                        <div class="text-white text-sm font-semibold truncate">{{ $c->title }}</div>
                        <div class="text-[10px] text-zinc-500 uppercase tracking-wide">{{ $c->category->name }}</div>
                    </div>
                    <div class="col-span-1 text-zinc-400 text-xs truncate">
                        {{ $c->is_anonymous ? 'Anonim' : $c->reporter_name }}
                    </div>
                    <div class="col-span-1">
                        <!-- Pakai text-transform tanpa badge kalau mau benar-benar raw -->
                        <span class="text-[10px] font-bold text-zinc-300 uppercase tracking-widest">{{ $c->status }}</span>
                    </div>
                    <div class="col-span-1 text-right">
                        <a href="{{ route('dashboard.pengaduan.show', $c->ticket_code) }}" class="text-[10px] font-bold text-white uppercase tracking-widest underline decoration-white/20 underline-offset-4 hover:decoration-white">
                            View
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center text-zinc-700 text-xs uppercase tracking-widest">Tidak ada data.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection