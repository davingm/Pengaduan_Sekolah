@extends('layouts.app')

@section('title', 'Masuk ke Sistem Pengaduan')

@section('content')
<div class="py-16 bg-black min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto px-4 space-y-6">
        
        <div class="text-center space-y-2">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-xl shadow-blue-500/25">
                <i data-lucide="lock" class="w-6 h-6"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Masuk ke Akun Anda
            </h1>
            <p class="text-xs text-slate-500">
                Pilih opsi login atau gunakan tombol demo 1-klik di bawah
            </p>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Alamat Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@sekolah.id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kata Sandi *</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600 dark:text-slate-400">
                        <input type="checkbox" name="remember" class="rounded text-blue-600 focus:ring-blue-500">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-500/25 transition text-xs">
                    Masuk Sekarang
                </button>
            </form>

            <!-- Quick Demo 1-Click Buttons -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center">
                    ⚡ Akses Cepat Demo (1-Klik Tanpa Password)
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="{{ route('demo.switch', 'siswa') }}" class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 font-semibold text-center transition">
                        Siswa / Pelapor
                    </a>
                    <a href="{{ route('demo.switch', 'guru_piket') }}" class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 hover:bg-amber-100 text-amber-700 dark:text-amber-300 font-semibold text-center transition">
                        Guru Piket (Verifikator)
                    </a>
                    <a href="{{ route('demo.switch', 'petugas') }}" class="p-2 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 hover:bg-blue-100 text-blue-700 dark:text-blue-300 font-semibold text-center transition">
                        Petugas Sarpras
                    </a>
                    <a href="{{ route('demo.switch', 'kepala_sekolah') }}" class="p-2 rounded-xl bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800/60 hover:bg-purple-100 text-purple-700 dark:text-purple-300 font-semibold text-center transition">
                        Kepala Sekolah
                    </a>
                </div>
            </div>

            <div class="text-center text-xs text-slate-500">
                Belum memiliki akun siswa? 
                <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">Daftar di sini</a>
            </div>

        </div>

    </div>
</div>
@endsection
