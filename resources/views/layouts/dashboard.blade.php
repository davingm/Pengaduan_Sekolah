<!DOCTYPE html>
<html lang="id" class="h-full bg-black text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Sistem Pengaduan Sekolah</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-black selection:bg-blue-600 selection:text-white" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-black/80 backdrop-blur-sm lg:hidden" 
             x-cloak></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-72 bg-black border-r border-slate-800 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col justify-between">
            
            <div>
                <!-- Brand Header -->
                <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                            <i data-lucide="megaphone" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <span class="font-bold text-sm text-white tracking-tight">SiPengaduan</span>
                            <span class="block text-[10px] text-blue-400 font-semibold uppercase tracking-wider">Dashboard Panel</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- User Profile Header in Sidebar -->
                <div class="p-4 mx-3 my-3 rounded-2xl bg-zinc-950 border border-slate-800 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</h4>
                        <div class="inline-flex items-center gap-1 mt-0.5">
                            <span class="text-[10px] px-2 py-0.5 rounded-md font-semibold border {{ Auth::user()->role_badge }}">
                                {{ Auth::user()->role_label }}
                            </span>
                        </div>
                        @if(Auth::user()->department)
                            <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ Auth::user()->department }}</p>
                        @endif
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="px-3 space-y-1">
                    <div class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        Menu Utama
                    </div>

                    <!-- Dashboard Home -->
                    <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard.index') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/80' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Pengaduan Menu -->
                    <a href="{{ route('dashboard.pengaduan.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard.pengaduan.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/80' }}">
                        <div class="flex items-center gap-3">
                            <i data-lucide="inbox" class="w-4 h-4"></i>
                            <span>{{ Auth::user()->isSiswa() ? 'Pengaduan Saya' : 'Daftar Pengaduan' }}</span>
                        </div>
                    </a>

                    @if(Auth::user()->isSiswa())
                        <!-- Buat Pengaduan Baru (Siswa) -->
                        <a href="{{ route('pengaduan.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-emerald-400 hover:bg-emerald-950/40 border border-emerald-500/20 transition">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            <span>+ Buat Pengaduan Baru</span>
                        </a>
                    @endif

                    @if(Auth::user()->isAdmin())
                        <div class="pt-4 px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            Master Data
                        </div>

                        <!-- Kategori (Admin) -->
                        <a href="{{ route('dashboard.kategori.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard.kategori.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/80' }}">
                            <i data-lucide="folder-tree" class="w-4 h-4"></i>
                            <span>Kategori Pengaduan</span>
                        </a>
                    @endif

                    @if(Auth::user()->isKepalaSekolah() || Auth::user()->isAdmin())
                        <div class="pt-4 px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            Eksekutif & Laporan
                        </div>

                        <!-- Laporan & Rekap -->
                        <a href="{{ route('dashboard.laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition {{ request()->routeIs('dashboard.laporan.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/80' }}">
                            <i data-lucide="file-bar-chart" class="w-4 h-4"></i>
                            <span>Laporan & Rekapitulasi</span>
                        </a>
                    @endif

                    <div class="pt-4 px-3 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        Akses Cepat
                    </div>

                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-medium text-slate-400 hover:text-white hover:bg-slate-800/80 transition">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        <span>Lihat Portal Publik</span>
                    </a>
                </nav>
            </div>

            <!-- Bottom Logout Area -->
            <div class="p-4 border-t border-slate-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold text-rose-400 hover:text-white hover:bg-rose-600/20 border border-rose-500/20 transition">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-black overflow-y-auto">
            
            <!-- Top Dashboard Bar -->
            <header class="h-16 bg-black/90 backdrop-blur-md border-b border-slate-800 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-800">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <div>
                        <h1 class="text-sm sm:text-base font-bold text-white tracking-tight">@yield('page_title', 'Dashboard')</h1>
                        <p class="text-[11px] text-slate-400 hidden sm:block">@yield('page_description', 'Sistem Pengaduan Sekolah Terintegrasi')</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('pengaduan.create') }}" class="hidden sm:inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-3 py-2 rounded-xl shadow-md shadow-blue-600/20 transition">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Buat Pengaduan</span>
                    </a>
                </div>
            </header>

            <!-- Toast Messages -->
            <x-toast />

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- Floating 1-Click Demo Role Switcher -->
    <x-demo-role-switcher />

</body>
</html>
