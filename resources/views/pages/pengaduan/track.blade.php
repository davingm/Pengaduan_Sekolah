@extends('layouts.app')

@section('title', 'Lacak Status Pengaduan')

@section('content')
<div class="min-h-screen bg-[#09090b] flex flex-col">

    <div class="flex-1 flex items-center justify-center px-5 sm:px-8 py-16 lg:py-24">
        <div class="w-full max-w-md space-y-10">

            <!-- Header -->
            <div class="text-center space-y-3">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-white/[0.04] border border-white/[0.06] flex items-center justify-center">
                    <i data-lucide="search" class="w-5 h-5 text-neutral-400"></i>
                </div>
                <h1 class="text-[28px] sm:text-[32px] font-bold text-white tracking-[-0.02em] leading-tight">
                    Lacak Pengaduan
                </h1>
                <p class="text-[15px] text-neutral-500 leading-relaxed max-w-sm mx-auto">
                    Masukkan kode tiket yang Anda terima saat mengirim laporan.
                </p>
            </div>

            <!-- Form Card -->
            <div class="bg-white/[0.03] border border-white/[0.06] rounded-2xl p-6 sm:p-8 space-y-6">

                <form action="{{ route('pengaduan.track') }}" method="GET" class="space-y-4">
                    <div class="space-y-2.5">
                        <label class="block text-[12px] font-semibold text-neutral-600 uppercase tracking-[0.08em]">
                            Nomor Tiket
                        </label>
                        <div class="relative group">
                            <i data-lucide="hash" class="w-[18px] h-[18px] absolute left-4 top-1/2 -translate-y-1/2 text-neutral-700 group-focus-within:text-neutral-400 transition-colors pointer-events-none"></i>
                            <input type="text"
                                   name="ticket"
                                   value="{{ request('ticket') }}"
                                   placeholder="PGD-202608-0001"
                                   required
                                   autocomplete="off"
                                   spellcheck="false"
                                   class="w-full bg-white/[0.03] border border-white/[0.08] rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder-neutral-700 font-mono uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent transition-shadow">
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2.5 bg-white hover:bg-neutral-200 text-[#09090b] font-semibold py-3.5 rounded-xl text-[14px] transition-colors duration-200 active:scale-[0.98] transform focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30 focus-visible:ring-offset-2 focus-visible:ring-offset-[#09090b]">
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        <span>Lacak</span>
                    </button>
                </form>

                <!-- Helper -->
                <div class="pt-5 border-t border-white/[0.06] space-y-2.5">
                    <div class="flex items-start gap-2.5 text-[13px]">
                        <i data-lucide="info" class="w-4 h-4 text-neutral-700 mt-0.5 shrink-0"></i>
                        <p class="text-neutral-500 leading-[1.65]">
                            Lupa kode tiket? Jika Anda melapor dalam keadaan login, buka
                            <a href="{{ route('dashboard.index') }}" class="text-neutral-300 hover:text-white underline underline-offset-2 decoration-neutral-700 hover:decoration-neutral-400 transition-colors">Dashboard Siswa</a>
                            untuk melihat seluruh riwayat.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Subtle footer -->
    <div class="py-6 text-center">
        <p class="text-[11px] text-neutral-800 font-mono tracking-wider">SISTEM PENGADUAN</p>
    </div>

</div>
@endsection