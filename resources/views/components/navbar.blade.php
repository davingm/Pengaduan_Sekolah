<nav x-data="{ mobileOpen: false }" class="sticky top-0 z-50 bg-black text-white border-b border-zinc-900">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        
        <!-- Left: Brand / Logo Style (Render style icon + text) -->
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
            <div class="text-white flex items-center justify-center">
                <!-- Custom Render-like Node Icon -->
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                </svg>
            </div>
            <span class="font-semibold text-base tracking-tight text-white">SiPengaduan</span>
        </a>

        <!-- Center: Minimal Clean Links (Render / Vercel style) -->
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-zinc-400">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors {{ request()->routeIs('home') ? 'text-white' : '' }}">
                Beranda
            </a>
            <a href="{{ route('pengaduan.create') }}" class="hover:text-white transition-colors {{ request()->routeIs('pengaduan.create') ? 'text-white' : '' }}">
                Buat Laporan
            </a>
            <a href="{{ route('pengaduan.track') }}" class="hover:text-white transition-colors {{ request()->routeIs('pengaduan.track') ? 'text-white' : '' }}">
                Lacak Tiket
            </a>
        </div>

        <!-- Right: Action / Auth (Clean minimalism) -->
        <div class="hidden md:flex items-center gap-4 text-sm font-medium">
            @if(Auth::check())
                <a href="{{ route('dashboard.index') }}" class="text-zinc-400 hover:text-white transition-colors">
                    Dashboard
                </a>
                <span class="text-zinc-700">/</span>
                <span class="text-xs font-mono text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded">{{ Auth::user()->role_label }}</span>
            @else
                <a href="{{ route('login') }}" class="text-zinc-400 hover:text-white transition-colors">
                    Sign In
                </a>
                <a href="{{ route('pengaduan.create') }}" class="bg-white text-black hover:bg-zinc-200 px-4 py-2 rounded-full text-xs font-semibold transition-all">
                    Get Started
                </a>
            @endif
        </div>

        <!-- Mobile Hamburger Menu Button -->
        <div class="flex md:hidden items-center">
            <button @click="mobileOpen = !mobileOpen" class="text-zinc-400 hover:text-white p-2">
                <i :data-lucide="mobileOpen ? 'x' : 'menu'" class="w-6 h-6"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div x-show="mobileOpen" x-collapse class="md:hidden bg-black border-b border-zinc-900 px-6 py-4 space-y-3 text-sm font-medium text-zinc-400" x-cloak>
        <a href="{{ route('home') }}" class="block hover:text-white py-1">Beranda</a>
        <a href="{{ route('pengaduan.create') }}" class="block hover:text-white py-1">Buat Laporan</a>
        <a href="{{ route('pengaduan.track') }}" class="block hover:text-white py-1">Lacak Tiket</a>
        <div class="pt-3 border-t border-zinc-900 flex flex-col gap-2">
            @if(Auth::check())
                <a href="{{ route('dashboard.index') }}" class="text-white py-1">Dashboard ({{ Auth::user()->role_label }})</a>
            @else
                <a href="{{ route('login') }}" class="text-white py-1">Sign In</a>
                <a href="{{ route('pengaduan.create') }}" class="w-full text-center bg-white text-black py-2 rounded-full text-xs font-semibold">Get Started</a>
            @endif
        </div>
    </div>
</nav>