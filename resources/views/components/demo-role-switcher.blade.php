@php
    $currentRole = Auth::check() ? Auth::user()->role : 'guest';
@endphp

<div x-data="{ open: false, minimized: false }" class="fixed bottom-4 right-4 z-50">
    <!-- Toggle Button -->
    <div x-show="!open" class="flex items-center gap-2 bg-slate-900/90 text-white backdrop-blur-md px-3.5 py-2 rounded-full shadow-2xl border border-slate-700/50 hover:bg-slate-800 transition cursor-pointer" @click="open = true">
        <span class="flex h-2.5 w-2.5 relative">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
        </span>
        <div class="flex items-center gap-1.5 text-xs font-medium">
            <span></span>
            <span class="font-bold text-emerald-400">DevTools</span>
        </div>
        <i data-lucide="chevron-up" class="w-3.5 h-3.5 text-slate-400"></i>
    </div>

    <!-- Expanded Switcher Modal/Card -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="bg-slate-900/95 text-white backdrop-blur-xl border border-slate-700/80 rounded-2xl p-4 shadow-2xl w-80 max-w-[calc(100vw-2rem)]"
         x-cloak>
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-800 mb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-600/30 border border-indigo-500/40 flex items-center justify-center text-indigo-400">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-100">Quick Role Switcher</h4>
                    <p class="text-[10px] text-slate-400">Uji coba 1-klik seluruh alur sistem</p>
                </div>
            </div>
            <button @click="open = false" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="space-y-1.5 max-h-72 overflow-y-auto pr-1">
            <!-- Siswa -->
            <a href="{{ route('demo.switch', 'siswa') }}" class="flex items-center justify-between p-2 rounded-xl text-xs transition {{ $currentRole === 'siswa' ? 'bg-emerald-600/20 border border-emerald-500/40 text-emerald-300' : 'bg-slate-800/60 hover:bg-slate-800 border border-transparent text-slate-200' }}">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold text-[10px]">SW</span>
                    <div>
                        <div class="font-semibold">Siswa / Pelapor</div>
                        <div class="text-[10px] text-slate-400">Ahmad Fauzan (XI MIPA 2)</div>
                    </div>
                </div>
                @if($currentRole === 'siswa')
                    <span class="text-[10px] bg-emerald-500 text-slate-950 font-bold px-1.5 py-0.5 rounded">Aktif</span>
                @endif
            </a>

            <!-- Guru Piket -->
            <a href="{{ route('demo.switch', 'guru_piket') }}" class="flex items-center justify-between p-2 rounded-xl text-xs transition {{ $currentRole === 'guru_piket' ? 'bg-amber-600/20 border border-amber-500/40 text-amber-300' : 'bg-slate-800/60 hover:bg-slate-800 border border-transparent text-slate-200' }}">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-amber-500/20 flex items-center justify-center text-amber-400 font-bold text-[10px]">PK</span>
                    <div>
                        <div class="font-semibold">Guru Piket (Verifikator)</div>
                        <div class="text-[10px] text-slate-400">Dra. Endang Sulistyowati</div>
                    </div>
                </div>
                @if($currentRole === 'guru_piket')
                    <span class="text-[10px] bg-amber-500 text-slate-950 font-bold px-1.5 py-0.5 rounded">Aktif</span>
                @endif
            </a>

            <!-- Petugas Sarpras -->
            <a href="{{ route('demo.switch', 'petugas') }}" class="flex items-center justify-between p-2 rounded-xl text-xs transition {{ $currentRole === 'petugas' ? 'bg-blue-600/20 border border-blue-500/40 text-blue-300' : 'bg-slate-800/60 hover:bg-slate-800 border border-transparent text-slate-200' }}">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400 font-bold text-[10px]">PT</span>
                    <div>
                        <div class="font-semibold">Petugas Sarpras / Penanganan</div>
                        <div class="text-[10px] text-slate-400">Joko Santoso, S.T.</div>
                    </div>
                </div>
                @if($currentRole === 'petugas')
                    <span class="text-[10px] bg-blue-500 text-slate-950 font-bold px-1.5 py-0.5 rounded">Aktif</span>
                @endif
            </a>

            <!-- Kepala Sekolah -->
            <a href="{{ route('demo.switch', 'kepala_sekolah') }}" class="flex items-center justify-between p-2 rounded-xl text-xs transition {{ $currentRole === 'kepala_sekolah' ? 'bg-purple-600/20 border border-purple-500/40 text-purple-300' : 'bg-slate-800/60 hover:bg-slate-800 border border-transparent text-slate-200' }}">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-purple-500/20 flex items-center justify-center text-purple-400 font-bold text-[10px]">KS</span>
                    <div>
                        <div class="font-semibold">Kepala Sekolah (Approver)</div>
                        <div class="text-[10px] text-slate-400">Dr. H. Mulyadi Subagyo</div>
                    </div>
                </div>
                @if($currentRole === 'kepala_sekolah')
                    <span class="text-[10px] bg-purple-500 text-slate-950 font-bold px-1.5 py-0.5 rounded">Aktif</span>
                @endif
            </a>

            <!-- Admin -->
            <a href="{{ route('demo.switch', 'admin') }}" class="flex items-center justify-between p-2 rounded-xl text-xs transition {{ $currentRole === 'admin' ? 'bg-rose-600/20 border border-rose-500/40 text-rose-300' : 'bg-slate-800/60 hover:bg-slate-800 border border-transparent text-slate-200' }}">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-rose-500/20 flex items-center justify-center text-rose-400 font-bold text-[10px]">AD</span>
                    <div>
                        <div class="font-semibold">Administrator</div>
                        <div class="text-[10px] text-slate-400">IT & Master Control</div>
                    </div>
                </div>
                @if($currentRole === 'admin')
                    <span class="text-[10px] bg-rose-500 text-slate-950 font-bold px-1.5 py-0.5 rounded">Aktif</span>
                @endif
            </a>
        </div>

        <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-[11px]">
            @if(Auth::check())
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-rose-400 hover:text-rose-300 font-medium flex items-center gap-1">
                        <i data-lucide="log-out" class="w-3 h-3"></i> Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-medium">Halaman Login &rarr;</a>
            @endif
            <a href="{{ route('home') }}" class="text-slate-400 hover:text-slate-200">Ke Beranda</a>
        </div>
    </div>
</div>
