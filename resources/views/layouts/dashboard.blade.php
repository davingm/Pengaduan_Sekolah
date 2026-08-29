<!DOCTYPE html>
<html lang="id" class="h-full bg-black text-zinc-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Sistem Pengaduan Sekolah</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#09090b] text-zinc-200 selection:bg-orange-500 selection:text-white" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-black/80 backdrop-blur-sm lg:hidden" 
             x-cloak></div>

        <!-- Sidebar (Cloudflare / shadcn style) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#09090b] border-r border-zinc-800/80 transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col justify-between">
            
            <div class="flex-1 overflow-y-auto">
                <!-- Brand Header -->
                <div class="h-14 flex items-center justify-between px-5 border-b border-zinc-800/80">
                    <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2.5 group">
                        <div class="w-7 h-7 rounded-lg bg-orange-500/10 border border-orange-500/30 flex items-center justify-center text-orange-400 group-hover:bg-orange-500 group-hover:text-black transition-all">
                            <i data-lucide="flame" class="w-4 h-4"></i>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm text-zinc-100 tracking-tight">SiPengaduan</span>
                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-zinc-900 border border-zinc-800 text-zinc-400">v2.0</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-zinc-500 hover:text-zinc-200 p-1">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Active User Summary in Sidebar -->
                <div class="p-3 mx-3 my-3 rounded-lg bg-zinc-900/40 border border-zinc-800/80 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-md bg-zinc-800 border border-zinc-700/60 flex items-center justify-center text-zinc-200 font-mono font-medium text-xs shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden flex-1 min-w-0">
                        <h4 class="text-xs font-medium text-zinc-200 truncate">{{ Auth::user()->name }}</h4>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                            <span class="text-[10px] font-mono text-zinc-400 truncate">
                                {{ Auth::user()->role_label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="px-3 space-y-1 pb-4">
                    <div class="px-2 pt-2 pb-1.5 text-[10px] font-mono font-semibold text-zinc-500 uppercase tracking-wider">
                        Overview
                    </div>

                    <!-- Dashboard Home -->
                    <a href="{{ route('dashboard.index') }}" 
                       class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('dashboard.index') ? 'bg-zinc-900 text-zinc-100 border border-zinc-700/80 shadow-xs' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900/50 border border-transparent' }}">
                        <i data-lucide="layout-grid" class="w-4 h-4 {{ request()->routeIs('dashboard.index') ? 'text-orange-400' : 'text-zinc-400' }}"></i>
                        <span>Ringkasan</span>
                    </a>

                    <div class="px-2 pt-4 pb-1.5 text-[10px] font-mono font-semibold text-zinc-500 uppercase tracking-wider">
                        Pengaduan
                    </div>

                    <!-- Pengaduan Menu -->
                    <a href="{{ route('dashboard.pengaduan.index') }}" 
                       class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('dashboard.pengaduan.*') ? 'bg-zinc-900 text-zinc-100 border border-zinc-700/80 shadow-xs' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900/50 border border-transparent' }}">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="inbox" class="w-4 h-4 {{ request()->routeIs('dashboard.pengaduan.*') ? 'text-orange-400' : 'text-zinc-400' }}"></i>
                            <span>{{ Auth::user()->isSiswa() ? 'Pengaduan Saya' : 'Daftar Pengaduan' }}</span>
                        </div>
                    </a>

                    @if(Auth::user()->isSiswa())
                        <!-- Buat Pengaduan Baru (Siswa) -->
                        <a href="{{ route('pengaduan.create') }}" 
                           class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-xs font-medium text-orange-400 hover:text-orange-300 hover:bg-orange-500/10 border border-orange-500/20 transition-all">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            <span>Buat Laporan Baru</span>
                        </a>
                    @endif

                    @if(Auth::user()->isAdmin())
                        <div class="px-2 pt-4 pb-1.5 text-[10px] font-mono font-semibold text-zinc-500 uppercase tracking-wider">
                            Master Data
                        </div>

                        <!-- Kategori (Admin) -->
                        <a href="{{ route('dashboard.kategori.index') }}" 
                           class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('dashboard.kategori.*') ? 'bg-zinc-900 text-zinc-100 border border-zinc-700/80 shadow-xs' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900/50 border border-transparent' }}">
                            <i data-lucide="folder-tree" class="w-4 h-4 {{ request()->routeIs('dashboard.kategori.*') ? 'text-orange-400' : 'text-zinc-400' }}"></i>
                            <span>Kategori Pengaduan</span>
                        </a>
                    @endif

                    @if(Auth::user()->isKepalaSekolah() || Auth::user()->isAdmin())
                        <div class="px-2 pt-4 pb-1.5 text-[10px] font-mono font-semibold text-zinc-500 uppercase tracking-wider">
                            Eksekutif
                        </div>

                        <!-- Laporan & Rekap -->
                        <a href="{{ route('dashboard.laporan.index') }}" 
                           class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-xs font-medium transition-all {{ request()->routeIs('dashboard.laporan.*') ? 'bg-zinc-900 text-zinc-100 border border-zinc-700/80 shadow-xs' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900/50 border border-transparent' }}">
                            <i data-lucide="file-bar-chart-2" class="w-4 h-4 {{ request()->routeIs('dashboard.laporan.*') ? 'text-orange-400' : 'text-zinc-400' }}"></i>
                            <span>Laporan & Rekapitulasi</span>
                        </a>
                    @endif

                    <div class="px-2 pt-4 pb-1.5 text-[10px] font-mono font-semibold text-zinc-500 uppercase tracking-wider">
                        External
                    </div>

                    <a href="{{ route('home') }}" 
                       class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-xs font-medium text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900/50 border border-transparent transition-all">
                        <i data-lucide="external-link" class="w-4 h-4 text-zinc-500"></i>
                        <span>Portal Publik</span>
                    </a>
                </nav>
            </div>

            <!-- Bottom Area -->
            <div class="p-3 border-t border-zinc-800/80">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-medium text-zinc-400 hover:text-red-400 hover:bg-red-500/10 border border-zinc-800/80 hover:border-red-500/20 transition-all">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span>Keluar Sesi</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#09090b] overflow-y-auto">
            
            <!-- Top Dashboard Bar -->
            <header class="h-14 bg-[#09090b]/80 backdrop-blur-md border-b border-zinc-800/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-zinc-400 hover:text-zinc-100 p-1.5 rounded-md hover:bg-zinc-900 border border-zinc-800">
                        <i data-lucide="menu" class="w-4 h-4"></i>
                    </button>
                    <div>
                        <h1 class="text-xs sm:text-sm font-semibold text-zinc-100 tracking-tight flex items-center gap-2">
                            <span>@yield('page_title', 'Dashboard')</span>
                            <span class="hidden sm:inline-block text-zinc-600 font-mono">&bull;</span>
                            <span class="hidden sm:inline-block text-[11px] font-normal text-zinc-400">@yield('page_description', 'Sistem Pengaduan Sekolah')</span>
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    @if(Auth::user()->isSiswa())
                        <a href="{{ route('pengaduan.create') }}" 
                           class="inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm transition-all">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Buat Laporan</span>
                        </a>
                    @else
                        <a href="{{ route('dashboard.pengaduan.index') }}" 
                           class="hidden sm:inline-flex items-center gap-1.5 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 text-xs font-medium px-3 py-1.5 rounded-lg transition-all">
                            <i data-lucide="inbox" class="w-3.5 h-3.5 text-zinc-400"></i>
                            <span>Semua Tiket</span>
                        </a>
                    @endif
                </div>
            </header>

            <!-- Toast Messages -->
            <x-toast />

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                @yield('content')
            </main>

        </div>
    </div>

</body>
</html>
