<footer class="bg-black text-white px-4 sm:px-6 lg:px-8 py-12">
    <!-- Kontainer utama dengan border tipis dan sudut melengkung khas footer modern SaaS -->
    <div class="max-w-7xl mx-auto bg-zinc-950 border border-zinc-900 rounded-3xl p-8 sm:p-12 relative overflow-hidden">
        
        <!-- Bagian Atas: Kiri (Brand & Deskripsi) & Kanan (Navigasi & Socials) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 pb-16 border-b border-zinc-900">
            
            <!-- Sisi Kiri: Logo & Info -->
            <div class="lg:col-span-6 space-y-6">
                <!-- Logo Style: Kotak Kontras -->
                <div class="inline-flex items-center bg-white text-black px-3.5 py-1.5 rounded-lg font-bold text-sm tracking-tight">
                    SiPengaduan<span class="bg-black text-white px-1.5 py-0.5 rounded ml-1.5 text-xs font-mono">ID</span>
                </div>
                
                <p class="text-zinc-400 text-sm max-w-sm leading-relaxed">
                    SiPengaduan adalah platform layanan pelaporan dan aspirasi warga sekolah terpadu. Aman, rahasia, dan transparan untuk lingkungan pendidikan yang lebih baik.
                </p>

                <!-- Tombol Aksi Khas (Opsional, mirip Join Waitlist di gambar) -->
                <div>
                    <a href="{{ route('pengaduan.create') }}" class="inline-flex items-center gap-2 bg-[#d4ff00] hover:bg-[#bfe600] text-black font-bold text-xs px-5 py-3 rounded-full tracking-wider uppercase transition-all">
                        <span>Buat Laporan Baru</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Sisi Kanan: Menu Navigasi & Sosial Media -->
            <div class="lg:col-span-6 grid grid-cols-2 gap-8 lg:justify-end">
                <!-- Kolom Navigasi -->
                <div>
                    <h4 class="text-[10px] font-mono uppercase tracking-[0.2em] text-zinc-500 mb-4">Navigasi</h4>
                    <ul class="space-y-3 text-sm font-medium text-zinc-300">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ route('pengaduan.create') }}" class="hover:text-white transition-colors">Buat Pengaduan</a></li>
                        <li><a href="{{ route('pengaduan.track') }}" class="hover:text-white transition-colors">Lacak Status</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk Petugas</a></li>
                    </ul>
                </div>

                <!-- Kolom Socials -->
                <div>
                    <h4 class="text-[10px] font-mono uppercase tracking-[0.2em] text-zinc-500 mb-4">Socials</h4>
                    <div class="flex items-center gap-3">
                        <a href="#" class="w-10 h-10 rounded-lg bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white hover:border-zinc-700 transition">
                            <i data-lucide="instagram" class="w-4 h-4"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white hover:border-zinc-700 transition">
                            <i data-lucide="globe" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Bagian Bawah: Copyright & Tautan Legal (Kebijakan Privasi, dll) -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500">
            <p>&copy; {{ date('Y') }} SiPengaduan. All Rights Reserved</p>
            
            <div class="flex items-center gap-6 font-medium">
                <a href="#" class="hover:text-white underline underline-offset-4 decoration-zinc-800 transition">Privacy Policy</a>
                <a href="#" class="hover:text-white underline underline-offset-4 decoration-zinc-800 transition">Term of Service</a>
            </div>

            <p class="text-zinc-600 font-mono text-[11px]">
                Powered by <span class="text-zinc-400 font-semibold">Laravel & Tailwind</span>
            </p>
        </div>

    </div>
</footer>