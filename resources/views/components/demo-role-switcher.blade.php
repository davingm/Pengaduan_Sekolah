@php
    $currentRole = Auth::check() ? Auth::user()->role : 'guest';
@endphp

<div x-data="{ open: false }" class="fixed bottom-5 right-5 z-50">
    <!-- Toggle Button -->
    <div x-show="!open" 
         @click="open = true"
         class="flex items-center gap-2.5 bg-zinc-950/90 text-zinc-300 backdrop-blur-md px-3.5 py-2 rounded-lg shadow-2xl border border-zinc-800 hover:border-zinc-700 hover:text-white transition-all cursor-pointer">
        <span class="flex h-2 w-2 relative">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
        </span>
        <div class="flex items-center gap-1.5 text-xs font-mono">
            <span class="text-zinc-500">role:</span>
            <span class="font-semibold text-orange-400">{{ $currentRole }}</span>
        </div>
        <i data-lucide="chevron-up" class="w-3.5 h-3.5 text-zinc-500"></i>
    </div>

    <!-- Expanded Switcher Modal/Card -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-3 scale-95"
         class="bg-zinc-950/95 text-zinc-100 backdrop-blur-xl border border-zinc-800 rounded-xl p-4 shadow-2xl w-80 max-w-[calc(100vw-2rem)]"
         @click.outside="open = false"
         x-cloak>
        
        <div class="flex items-center justify-between pb-3 border-b border-zinc-800 mb-3">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-md bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-400">
                    <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-zinc-100">Role Switcher</h4>
                    <p class="text-[10px] text-zinc-500 font-mono">Quick auth switch for demo</p>
                </div>
            </div>
            <button @click="open = false" class="text-zinc-500 hover:text-zinc-300 p-1 rounded-md transition">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>

        <div class="space-y-1.5 max-h-72 overflow-y-auto pr-1">
            <!-- Siswa -->
            <a href="{{ route('demo.switch', 'siswa') }}" class="flex items-center justify-between p-2 rounded-lg text-xs transition border {{ $currentRole === 'siswa' ? 'bg-zinc-900 border-orange-500/40 text-zinc-100' : 'border-transparent hover:bg-zinc-900/60 text-zinc-400 hover:text-zinc-200' }}">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-md bg-zinc-800 border border-zinc-700 flex items-center justify-center text-zinc-300 font-mono text-[10px]">SW</span>
                    <div>
                        <div class="font-medium text-zinc-200">Siswa / Pelapor</div>
                        <div class="text-[10px] text-zinc-500">Ahmad Fauzan (XI MIPA 2)</div>
                    </div>
                </div>
                @if($currentRole === 'siswa')
                    <span class="text-[9px] bg-orange-500/20 text-orange-400 font-mono font-medium px-1.5 py-0.5 rounded border border-orange-500/30">Active</span>
                @endif
            </a>

            <!-- Guru Piket -->
            <a href="{{ route('demo.switch', 'guru_piket') }}" class="flex items-center justify-between p-2 rounded-lg text-xs transition border {{ $currentRole === 'guru_piket' ? 'bg-zinc-900 border-orange-500/40 text-zinc-100' : 'border-transparent hover:bg-zinc-900/60 text-zinc-400 hover:text-zinc-200' }}">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-md bg-zinc-800 border border-zinc-700 flex items-center justify-center text-zinc-300 font-mono text-[10px]">GP</span>
                    <div>
                        <div class="font-medium text-zinc-200">Guru Piket (Verifikator)</div>
                        <div class="text-[10px] text-zinc-500">Dra. Endang Sulistyowati</div>
                    </div>
                </div>
                @if($currentRole === 'guru_piket')
                    <span class="text-[9px] bg-orange-500/20 text-orange-400 font-mono font-medium px-1.5 py-0.5 rounded border border-orange-500/30">Active</span>
                @endif
            </a>

            <!-- Petugas Sarpras -->
            <a href="{{ route('demo.switch', 'petugas') }}" class="flex items-center justify-between p-2 rounded-lg text-xs transition border {{ $currentRole === 'petugas' ? 'bg-zinc-900 border-orange-500/40 text-zinc-100' : 'border-transparent hover:bg-zinc-900/60 text-zinc-400 hover:text-zinc-200' }}">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-md bg-zinc-800 border border-zinc-700 flex items-center justify-center text-zinc-300 font-mono text-[10px]">PT</span>
                    <div>
                        <div class="font-medium text-zinc-200">Petugas Sarpras (Teknisi)</div>
                        <div class="text-[10px] text-zinc-500">Joko Santoso, S.T.</div>
                    </div>
                </div>
                @if($currentRole === 'petugas')
                    <span class="text-[9px] bg-orange-500/20 text-orange-400 font-mono font-medium px-1.5 py-0.5 rounded border border-orange-500/30">Active</span>
                @endif
            </a>

            <!-- Kepala Sekolah -->
            <a href="{{ route('demo.switch', 'kepala_sekolah') }}" class="flex items-center justify-between p-2 rounded-lg text-xs transition border {{ $currentRole === 'kepala_sekolah' ? 'bg-zinc-900 border-orange-500/40 text-zinc-100' : 'border-transparent hover:bg-zinc-900/60 text-zinc-400 hover:text-zinc-200' }}">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-md bg-zinc-800 border border-zinc-700 flex items-center justify-center text-zinc-300 font-mono text-[10px]">KS</span>
                    <div>
                        <div class="font-medium text-zinc-200">Kepala Sekolah (Approver)</div>
                        <div class="text-[10px] text-zinc-500">Dr. H. Mulyadi Subagyo</div>
                    </div>
                </div>
                @if($currentRole === 'kepala_sekolah')
                    <span class="text-[9px] bg-orange-500/20 text-orange-400 font-mono font-medium px-1.5 py-0.5 rounded border border-orange-500/30">Active</span>
                @endif
            </a>

            <!-- Admin -->
            <a href="{{ route('demo.switch', 'admin') }}" class="flex items-center justify-between p-2 rounded-lg text-xs transition border {{ $currentRole === 'admin' ? 'bg-zinc-900 border-orange-500/40 text-zinc-100' : 'border-transparent hover:bg-zinc-900/60 text-zinc-400 hover:text-zinc-200' }}">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-md bg-zinc-800 border border-zinc-700 flex items-center justify-center text-zinc-300 font-mono text-[10px]">AD</span>
                    <div>
                        <div class="font-medium text-zinc-200">Administrator</div>
                        <div class="text-[10px] text-zinc-500">IT & Master Control</div>
                    </div>
                </div>
                @if($currentRole === 'admin')
                    <span class="text-[9px] bg-orange-500/20 text-orange-400 font-mono font-medium px-1.5 py-0.5 rounded border border-orange-500/30">Active</span>
                @endif
            </a>
        </div>

        <div class="mt-3 pt-2.5 border-t border-zinc-800/80 flex items-center justify-between text-[11px] font-mono">
            @if(Auth::check())
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-zinc-400 hover:text-red-400 transition flex items-center gap-1">
                        <i data-lucide="log-out" class="w-3 h-3"></i> Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-orange-400 hover:underline">Login &rarr;</a>
            @endif
            <a href="{{ route('home') }}" class="text-zinc-500 hover:text-zinc-300">Portal Publik &rarr;</a>
        </div>
    </div>
</div>
