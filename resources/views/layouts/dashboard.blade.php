<!DOCTYPE html>
<html lang="id" class="h-full bg-black text-zinc-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Sistem Pengaduan Sekolah</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#000000] text-zinc-200 selection:bg-orange-500 selection:text-white font-sans" 
      x-data="{ 
          sidebarOpen: false, 
          searchModal: false, 
          searchQuery: '',
          userMenuOpen: false,
          liveRefreshing: false,
          triggerRefresh() {
              this.liveRefreshing = true;
              setTimeout(() => {
                  window.location.reload();
              }, 400);
          }
      }"
      @keydown.window.prevent.ctrl.k="searchModal = true"
      @keydown.window.prevent.cmd.k="searchModal = true"
      @keydown.escape.window="searchModal = false; userMenuOpen = false">

    <div class="min-h-screen bg-[#000000] text-zinc-200">
        
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

        <!-- Sidebar (Fixed Viewport Height & Independent Scroll) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 h-screen max-h-screen bg-[#09090b] border-r border-zinc-800/80 transition-transform duration-200 ease-in-out lg:translate-x-0 flex flex-col justify-between select-none">
            
            <div class="flex-1 overflow-y-auto flex flex-col">
                
                <!-- Account / Workspace Header -->
                <div class="h-14 px-3.5 border-b border-zinc-800/80 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2.5 w-full overflow-hidden">
                        <!-- Brand Icon -->
                        <div class="w-6 h-6 rounded-md bg-zinc-100 text-zinc-950 flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                            <i data-lucide="shield" class="w-3.5 h-3.5 fill-zinc-950 text-zinc-950"></i>
                        </div>
                        
                        <!-- Account Selector Pill -->
                        <div class="flex items-center justify-between flex-1 min-w-0 pr-1 group cursor-pointer" @click="userMenuOpen = !userMenuOpen">
                            <div class="truncate">
                                <div class="text-xs font-medium text-zinc-200 truncate group-hover:text-zinc-100 transition-colors">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-[10px] font-mono text-zinc-500 truncate flex items-center gap-1">
                                    <span>{{ Auth::user()->role_label }}</span>
                                </div>
                            </div>
                            <i data-lucide="chevrons-up-down" class="w-3.5 h-3.5 text-zinc-500 group-hover:text-zinc-300 shrink-0 ml-1"></i>
                        </div>
                    </div>

                    <button @click="sidebarOpen = false" class="lg:hidden text-zinc-400 hover:text-zinc-100 p-1">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Quick Search Input -->
                <div class="p-3 shrink-0">
                    <button @click="searchModal = true" 
                            type="button"
                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-md bg-[#121215] border border-zinc-800 hover:border-zinc-700 text-zinc-400 text-xs transition-all group">
                        <div class="flex items-center gap-2">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-zinc-500 group-hover:text-zinc-300"></i>
                            <span class="text-zinc-500 group-hover:text-zinc-400 text-xs">Cari...</span>
                        </div>
                        <div class="flex items-center gap-1 font-mono text-[10px] text-zinc-500">
                            <kbd class="geist-kbd">Ctrl</kbd>
                            <kbd class="geist-kbd">K</kbd>
                        </div>
                    </button>
                </div>

                <!-- Navigation Section Groups -->
                <nav class="px-2.5 space-y-4 pb-4 flex-1">
                    
                    <!-- Group: Overview -->
                    <div class="space-y-0.5">
                        <a href="{{ route('dashboard.index') }}" 
                           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('dashboard.index') ? 'bg-zinc-800/80 text-zinc-100 font-semibold border border-zinc-700/60' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900 border border-transparent' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('dashboard.index') ? 'text-zinc-100' : 'text-zinc-500' }}"></i>
                            <span>Dashboard Utama</span>
                        </a>
                    </div>

                    <!-- Group: Manage & Tickets -->
                    <div class="space-y-1">
                        <div class="px-2 text-[10px] font-mono font-medium text-zinc-500 uppercase tracking-wider">
                            Pengaduan
                        </div>

                        <!-- Tiket Pengaduan -->
                        <a href="{{ route('dashboard.pengaduan.index') }}" 
                           class="flex items-center justify-between px-2.5 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('dashboard.pengaduan.*') ? 'bg-zinc-800/80 text-zinc-100 font-semibold border border-zinc-700/60' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900 border border-transparent' }}">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="inbox" class="w-4 h-4 {{ request()->routeIs('dashboard.pengaduan.*') ? 'text-zinc-100' : 'text-zinc-500' }}"></i>
                                <span>{{ Auth::user()->isSiswa() ? 'Pengaduan Saya' : 'Tiket & Disposisi' }}</span>
                            </div>
                        </a>

                        @if(Auth::user()->isSiswa())
                            <a href="{{ route('pengaduan.create') }}" 
                               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-xs font-medium text-zinc-300 hover:text-zinc-100 hover:bg-zinc-800/50 border border-zinc-800 transition-all">
                                <i data-lucide="plus" class="w-4 h-4 text-zinc-400"></i>
                                <span>Buat Laporan Baru</span>
                            </a>
                        @endif
                    </div>

                    <!-- Group: Analytics & Intelligence (Kepsek & Admin) -->
                    @if(Auth::user()->isKepalaSekolah() || Auth::user()->isAdmin())
                        <div class="space-y-1">
                            <div class="px-2 text-[10px] font-mono font-medium text-zinc-500 uppercase tracking-wider">
                                Laporan
                            </div>

                            <a href="{{ route('dashboard.laporan.index') }}" 
                               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('dashboard.laporan.*') ? 'bg-zinc-800/80 text-zinc-100 font-semibold border border-zinc-700/60' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900 border border-transparent' }}">
                                <i data-lucide="bar-chart-3" class="w-4 h-4 {{ request()->routeIs('dashboard.laporan.*') ? 'text-zinc-100' : 'text-zinc-500' }}"></i>
                                <span>Rekap & Laporan</span>
                            </a>
                        </div>
                    @endif

                    <!-- Group: Build & Master Data (Admin) -->
                    @if(Auth::user()->isAdmin())
                        <div class="space-y-1">
                            <div class="px-2 text-[10px] font-mono font-medium text-zinc-500 uppercase tracking-wider">
                                Konfigurasi
                            </div>

                            <a href="{{ route('dashboard.kategori.index') }}" 
                               class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-xs font-medium transition-all {{ request()->routeIs('dashboard.kategori.*') ? 'bg-zinc-800/80 text-zinc-100 font-semibold border border-zinc-700/60' : 'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900 border border-transparent' }}">
                                <i data-lucide="layers" class="w-4 h-4 {{ request()->routeIs('dashboard.kategori.*') ? 'text-zinc-100' : 'text-zinc-500' }}"></i>
                                <span>Kategori Pengaduan</span>
                            </a>
                        </div>
                    @endif

                    <!-- Group: Public & External -->
                    <div class="space-y-1">
                        <div class="px-2 text-[10px] font-mono font-medium text-zinc-500 uppercase tracking-wider">
                            Navigasi
                        </div>

                        <a href="{{ route('home') }}" 
                           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-xs font-medium text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900 border border-transparent transition-all">
                            <i data-lucide="external-link" class="w-4 h-4 text-zinc-500"></i>
                            <span>Portal Siswa Publik</span>
                        </a>
                        <a href="{{ route('pengaduan.track') }}" 
                           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-xs font-medium text-zinc-400 hover:text-zinc-200 hover:bg-zinc-900 border border-transparent transition-all">
                            <i data-lucide="search" class="w-4 h-4 text-zinc-500"></i>
                            <span>Lacak Tiket Instan</span>
                        </a>
                    </div>
                </nav>
            </div>

            <!-- Sidebar Bottom: System Health & Logout -->
            <div class="p-3 border-t border-zinc-800/80 bg-[#09090b] space-y-2.5 shrink-0">
                <!-- System Status Indicator -->
                <div class="px-2 py-1.5 rounded-md bg-[#121215] border border-zinc-800/80 flex items-center justify-between text-[11px] font-mono">
                    <div class="flex items-center gap-2 text-zinc-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span class="text-zinc-300">Sistem Aktif</span>
                    </div>
                    <span class="text-[10px] text-zinc-500">v2.0</span>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 px-3 py-1.5 rounded-md text-xs font-medium text-zinc-400 hover:text-red-400 hover:bg-red-500/10 border border-zinc-800/60 hover:border-red-500/20 transition-all">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span>Keluar Sesi</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Canvas Area (Separated with lg:pl-64 & Independent Scroll) -->
        <div class="flex-1 flex flex-col min-w-0 bg-[#000000] lg:pl-64 min-h-screen">
            
            <!-- Top Navigation Header Bar -->
            <header class="h-14 bg-[#09090b]/80 backdrop-blur-md border-b border-zinc-800/80 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 select-none">
                
                <!-- Left: Breadcrumb Navigation -->
                <div class="flex items-center gap-2.5">
                    <button @click="sidebarOpen = true" class="lg:hidden text-zinc-400 hover:text-zinc-100 p-1.5 rounded-md hover:bg-zinc-900 border border-zinc-800">
                        <i data-lucide="menu" class="w-4 h-4"></i>
                    </button>

                    <div class="flex items-center gap-2 text-xs font-mono">
                        <span class="text-zinc-500">Dashboard</span>
                        <i data-lucide="chevron-right" class="w-3 h-3 text-zinc-600"></i>
                        <span class="text-zinc-200 font-medium font-sans text-xs sm:text-sm tracking-tight">@yield('page_title', 'Overview')</span>
                    </div>
                </div>

                <!-- Right: Utility Actions -->
                <div class="flex items-center gap-2">
                    
                    <!-- Search Button -->
                    <button @click="searchModal = true" 
                            class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-[#121215] hover:bg-[#18181b] text-zinc-300 text-xs font-medium border border-zinc-800 transition-colors">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-zinc-400"></i>
                        <span>Cari</span>
                        <kbd class="geist-kbd ml-1">Ctrl+K</kbd>
                    </button>

                    <!-- User Avatar Chip -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="w-8 h-8 rounded-full bg-zinc-800 border border-zinc-700/80 flex items-center justify-center text-zinc-200 text-xs font-mono font-medium hover:border-zinc-500 transition-colors">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </button>

                        <div x-show="open" 
                             @click.outside="open = false" 
                             x-transition 
                             class="absolute right-0 mt-2 w-56 bg-[#0c0c0e] border border-zinc-800 rounded-lg shadow-2xl p-1.5 z-50" 
                             x-cloak>
                            <div class="px-2.5 py-2 border-b border-zinc-800/80">
                                <div class="text-xs font-medium text-zinc-100 truncate">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] font-mono text-zinc-500 truncate">{{ Auth::user()->email }}</div>
                            </div>
                            <div class="pt-1">
                                <a href="{{ route('dashboard.pengaduan.index') }}" class="block px-2.5 py-1.5 text-xs text-zinc-400 hover:text-zinc-100 hover:bg-[#18181b] rounded-md transition-colors">
                                    Daftar Pengaduan
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="mt-1 pt-1 border-t border-zinc-800">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-2.5 py-1.5 text-xs text-red-400 hover:bg-red-500/10 rounded-md transition-colors">
                                        Keluar Sesi
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <!-- Toast Messages -->
            <x-toast />

            <!-- Page Main Content Area -->
            <main class="flex-1 p-4 sm:p-6 lg:p-7 max-w-[1400px] w-full mx-auto">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- Command Palette (Ctrl+K Modal ala Vercel Geist / Shadcn) -->
    <div x-show="searchModal" 
         class="fixed inset-0 z-50 flex items-start justify-center pt-20 p-4 bg-black/80 backdrop-blur-md" 
         x-cloak>
        <div class="bg-[#0c0c0e] border border-zinc-800 rounded-xl max-w-xl w-full shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150" 
             @click.outside="searchModal = false">
            
            <!-- Search Input Field -->
            <div class="h-12 border-b border-zinc-800 px-4 flex items-center gap-3">
                <i data-lucide="search" class="w-4 h-4 text-zinc-500"></i>
                <input type="text" 
                       x-model="searchQuery"
                       placeholder="Cari menu, nomor tiket, atau tindakan sistem..." 
                       class="w-full bg-transparent text-xs sm:text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none font-sans" 
                       autofocus>
                <kbd class="geist-kbd">ESC</kbd>
            </div>

            <!-- Command Results List -->
            <div class="max-h-80 overflow-y-auto p-2 space-y-1 text-xs">
                <div class="px-2.5 py-1 text-[10px] font-mono text-zinc-500 uppercase tracking-wider">Aksi & Navigasi Cepat</div>
                
                <a href="{{ route('dashboard.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-[#18181b] text-zinc-200 group transition-colors">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="activity" class="w-4 h-4 text-orange-400"></i>
                        <span>Buka Traffic Overview</span>
                    </div>
                    <span class="text-[10px] font-mono text-zinc-500">Jump to</span>
                </a>

                <a href="{{ route('dashboard.pengaduan.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-[#18181b] text-zinc-200 group transition-colors">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="inbox" class="w-4 h-4 text-zinc-400"></i>
                        <span>Semua Tiket Pengaduan</span>
                    </div>
                    <span class="text-[10px] font-mono text-zinc-500">Jump to</span>
                </a>

                @if(Auth::user()->isSiswa())
                    <a href="{{ route('pengaduan.create') }}" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-[#18181b] text-zinc-200 group transition-colors">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-orange-400"></i>
                            <span>Buat Pengaduan Baru</span>
                        </div>
                        <span class="text-[10px] font-mono text-zinc-500">Action</span>
                    </a>
                @endif

                @if(Auth::user()->isAdmin())
                    <a href="{{ route('dashboard.kategori.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-[#18181b] text-zinc-200 group transition-colors">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="layers" class="w-4 h-4 text-zinc-400"></i>
                            <span>Kelola Kategori Master</span>
                        </div>
                        <span class="text-[10px] font-mono text-zinc-500">Jump to</span>
                    </a>
                @endif

                <a href="{{ route('pengaduan.track') }}" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-[#18181b] text-zinc-200 group transition-colors">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="search" class="w-4 h-4 text-zinc-400"></i>
                        <span>Lacak Tiket Publik</span>
                    </div>
                    <span class="text-[10px] font-mono text-zinc-500">Public</span>
                </a>
            </div>

            <!-- Footer Hint -->
            <div class="px-4 py-2 border-t border-zinc-800 bg-[#08080a] flex items-center justify-between text-[11px] font-mono text-zinc-500">
                <div class="flex items-center gap-2">
                    <span>Tekan <kbd class="geist-kbd">↵</kbd> untuk pilih</span>
                    <span><kbd class="geist-kbd">ESC</kbd> untuk tutup</span>
                </div>
                <span>SiPengaduan v2.0</span>
            </div>
        </div>
    </div>

</body>
</html>
