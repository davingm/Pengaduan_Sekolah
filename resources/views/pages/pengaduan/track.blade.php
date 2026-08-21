@extends('layouts.app')

@section('title', 'Lacak Status Pengaduan')

@section('content')
<div class="py-16 bg-black min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-10 space-y-2">
            <div class="w-14 h-14 mx-auto rounded-3xl bg-blue-600/10 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-4">
                <i data-lucide="search" class="w-7 h-7"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                Lacak Status Pengaduan
            </h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto">
                Masukkan kode tiket unik yang Anda dapatkan saat mengirimkan laporan pengaduan.
            </p>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            
            <form action="{{ route('pengaduan.track') }}" method="GET" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Nomor Tiket Pengaduan
                    </label>
                    <div class="relative">
                        <i data-lucide="ticket" class="w-5 h-5 absolute left-3.5 top-3.5 text-slate-400"></i>
                        <input type="text" 
                               name="ticket" 
                               value="{{ request('ticket') }}"
                               placeholder="Contoh: PGD-202608-0001" 
                               required
                               class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl pl-11 pr-4 py-3.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:outline-none uppercase font-mono tracking-wider">
                    </div>
                </div>

                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-3.5 rounded-2xl shadow-xl shadow-blue-500/25 transition transform active:scale-95 text-sm">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>Cari Informasi Tiket</span>
                </button>
            </form>

            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                    <i data-lucide="help-circle" class="w-4 h-4 text-blue-500"></i>
                    <span>Lupa Kode Tiket Anda?</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Jika saat melapor Anda dalam keadaan login, Anda dapat melihat seluruh riwayat pengaduan langsung melalui <a href="{{ route('dashboard.index') }}" class="text-blue-600 font-bold hover:underline">Dashboard Siswa</a>.
                </p>
            </div>

        </div>

    </div>
</div>
@endsection
