@extends('layouts.app')

@section('title', 'Masuk — Sistem Pengaduan Sekolah')

@section('content')
<div x-data="{ showPw: false }" class="min-h-screen flex bg-[#0a0a0a]">

    {{-- ─────────────────────────────────────────
         KIRI — Form area (tidak pakai card/kotak)
    ───────────────────────────────────────── --}}
    <div class="flex flex-col w-full lg:w-[480px] xl:w-[520px] flex-shrink-0 px-10 sm:px-16 xl:px-20 py-10">

        {{-- Navbar top --}}
        <div class="flex items-center justify-between mb-auto">
            <a href="{{ route('home') }}" class="flex items-center gap-2 opacity-60 hover:opacity-100 transition-opacity">
                <i data-lucide="shield-check" class="w-4 h-4 text-white"></i>
                <span class="text-[12.5px] font-medium text-white tracking-tight">SiPengadu</span>
            </a>
            <a href="{{ route('register') }}" class="text-[12px] text-neutral-500 hover:text-white transition-colors">
                Daftar akun
            </a>
        </div>

        {{-- Form area — centered vertically --}}
        <div class="flex-1 flex flex-col justify-center py-16">
            <div class="w-full max-w-[340px]">

                {{-- Heading --}}
                <p class="text-[11px] font-semibold tracking-[0.18em] text-neutral-600 uppercase mb-3">Sistem Pengaduan Sekolah</p>
                <h1 class="text-[30px] sm:text-[34px] font-bold text-white leading-[1.15] tracking-[-0.03em] mb-2">
                    Masuk ke akun<br>Anda
                </h1>
                <p class="text-[13.5px] text-neutral-600 mb-10">
                    Kelola dan pantau laporan pengaduan sekolah.
                </p>

                {{-- Error --}}
                @if ($errors->any())
                    <p class="flex items-center gap-2 text-[12.5px] text-red-400 mb-7 -mt-4">
                        <i data-lucide="circle-alert" class="w-3.5 h-3.5 flex-shrink-0"></i>
                        {{ $errors->first() }}
                    </p>
                @endif
                @if (session('success'))
                    <p class="flex items-center gap-2 text-[12.5px] text-emerald-400 mb-7 -mt-4">
                        <i data-lucide="circle-check" class="w-3.5 h-3.5 flex-shrink-0"></i>
                        {{ session('success') }}
                    </p>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-7">
                    @csrf

                    {{-- Email --}}
                    <div class="group">
                        <label for="email" class="block text-[10.5px] font-bold tracking-[0.15em] uppercase text-neutral-600 group-focus-within:text-white transition-colors mb-2.5">
                            Alamat Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            placeholder="nama@sekolah.id"
                            class="w-full bg-transparent border-0 border-b-[1.5px] border-neutral-800 focus:border-white py-2.5 text-[15px] text-white placeholder-neutral-800 outline-none transition-all duration-200 @error('email') border-red-800 @enderror"
                        >
                    </div>

                    {{-- Password --}}
                    <div class="group">
                        <label for="password" class="block text-[10.5px] font-bold tracking-[0.15em] uppercase text-neutral-600 group-focus-within:text-white transition-colors mb-2.5">
                            Kata Sandi
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                :type="showPw ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full bg-transparent border-0 border-b-[1.5px] border-neutral-800 focus:border-white py-2.5 pr-8 text-[15px] text-white placeholder-neutral-800 outline-none transition-all duration-200"
                            >
                            <button
                                type="button"
                                @click="showPw = !showPw"
                                class="absolute right-0.5 bottom-2.5 text-neutral-700 hover:text-neutral-300 transition-colors"
                            >
                                <i x-show="!showPw" data-lucide="eye"     class="w-[15px] h-[15px]" x-cloak></i>
                                <i x-show="showPw"  data-lucide="eye-off" class="w-[15px] h-[15px]" x-cloak></i>
                            </button>
                        </div>
                    </div>

                    {{-- Remember --}}
                    <label class="flex items-center gap-2.5 cursor-pointer group/check w-fit">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 accent-white rounded cursor-pointer">
                        <span class="text-[12px] text-neutral-600 group-hover/check:text-neutral-400 select-none transition-colors">Ingat saya</span>
                    </label>

                    {{-- CTA --}}
                    <button
                        type="submit"
                        class="w-full py-3 bg-white text-[#0a0a0a] text-[13.5px] font-bold rounded-md hover:bg-neutral-100 active:scale-[0.985] transition-all duration-150 mt-2"
                    >
                        Masuk
                    </button>
                </form>
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-[11.5px] text-neutral-800">
            &copy; {{ date('Y') }} SiPengadu Sekolah
        </p>
    </div>

    {{-- ─────────────────────────────────────────
         KANAN — Full bleed image, no text
    ───────────────────────────────────────── --}}
    <div class="hidden lg:block flex-1 relative overflow-hidden">
        <img
            src="{{ asset('images/background.png') }}"
            alt=""
            class="absolute inset-0 w-full h-full object-cover"
            aria-hidden="true"
        >
        {{-- subtle left-edge gradient to blend with form panel --}}
        <div class="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-[#0a0a0a] to-transparent pointer-events-none"></div>
    </div>

</div>
@endsection
