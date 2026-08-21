@extends('layouts.app')
@section('title', 'Layanan Pengaduan & Aspirasi Siswa Terpadu')
@section('content')

<div class="fixed inset-0 -z-10 bg-black"></div>
<div class="home-page mx-3 mt-5 min-h-screen overflow-hidden rounded-[1.75rem] bg-black shadow-2xl sm:mx-5 lg:mx-8">
<!-- Hero Section -->
<section class="relative bg-black text-white overflow-hidden pt-32 pb-24 lg:pt-40 lg:pb-32 border-b border-zinc-800">
    <div class="absolute inset-x-0 top-0 bottom-16 bg-cover bg-center lg:bottom-24" style="background-image: linear-gradient(rgba(0, 0, 0, 0.32), rgba(0, 0, 0, 0.55)), url('{{ asset('images/background.png') }}');"></div>
    <!-- Subtle Geometric Glow (Vercel Style Ambient Light) -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[350px] bg-gradient-to-b from-zinc-800/40 via-zinc-900/10 to-transparent blur-[140px] pointer-events-none"></div>
    <div class="relative max-w-6xl mx-auto px-2 sm:px-6 lg:px-8 text-center space-y-10">

        <!-- Hero Heading: Pure Modern Typography -->
        <div class="space-y-4 max-w-3xl mx-auto">
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight text-white leading-[1.05]">
                Suara kamu. <br>
                <span class="text-zinc-500">Tanpa kompromi.</span>
            </h1>
            <p class="text-zinc-400 text-base sm:text-lg font-normal leading-relaxed">
                Infrastruktur pelaporan modern untuk lingkungan sekolah yang transparan, akuntabel, dan sepenuhnya rahasia. Laporkan kendala, lacak progres real-time dalam satu platform terpadu.
            </p>
        </div>

        <!-- Action Grid / Command Center (Rebranding Core) -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-4">
            <a href="{{ route('pengaduan.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-black hover:bg-zinc-200 font-medium text-sm px-6 py-3.5 transition-all shadow-[0_0_20px_rgba(255,255,255,0.1)]">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Buat Pengaduan Baru</span>
            </a>

            <a href="{{ route('pengaduan.track') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-zinc-900/80 hover:bg-zinc-800 border border-zinc-800 hover:border-zinc-700 text-zinc-300 font-medium text-sm px-6 py-3.5 transition-all">
                <i data-lucide="terminal" class="w-4 h-4 text-zinc-500"></i>
                <span>Lacak Status Tiket</span>
            </a>
        </div>

    <div class="pt-8">
        <div class="w-full max-w-[90rem] mx-auto px-4 pt-8">
            <div class="relative w-full aspect-video bg-black rounded-2xl overflow-hidden shadow-2xl border border-zinc-800">
                <iframe
                    class="w-full h-full"
                    src="https://www.youtube.com/embed/IQ379omimj4?si=apRoywgpdxXmVpxd"
                    title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen
                ></iframe>
            </div>
        </div>

    </div>
</section>

<!-- Lindungi Section -->
<section class="bg-black py-16 sm:py-20">
    <div class="mx-auto max-w-6xl px-4 text-center sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl space-y-4">
            <h2 class="text-4xl font-black tracking-tight text-white sm:text-5xl">Lindungi</h2>
            <p class="text-base leading-relaxed text-zinc-400 sm:text-lg">
                Jaga keamanan dan kenyamanan lingkungan sekolah dengan berani menyampaikan laporan secara aman, rahasia, dan bertanggung jawab.
            </p>
        </div>

        <!-- Perbesar ukuran kotak dengan menaikkan max-w dan lebar persentasenya -->
        <div class="mt-12 flex items-center justify-center gap-4 sm:gap-6">
            <div class="aspect-[3/5] w-[32%] max-w-[340px] overflow-hidden rounded-xl border border-[#eee5d5] bg-[#fffaf0] shadow-2xl">
                <img src="{{ asset('images/fact1.png') }}" alt="Ilustrasi perlindungan pertama" class="h-full w-full object-contain" loading="lazy">
            </div>
            <div class="aspect-[3/5] w-[32%] max-w-[340px] overflow-hidden rounded-xl border border-[#eee5d5] bg-[#fffaf0] shadow-2xl">
                <img src="{{ asset('images/fact2.png') }}" alt="Ilustrasi perlindungan kedua" class="h-full w-full object-contain" loading="lazy">
            </div>
            <div class="aspect-[3/5] w-[32%] max-w-[340px] overflow-hidden rounded-xl border border-[#eee5d5] bg-[#fffaf0] shadow-2xl">
                <img src="{{ asset('images/fact3.png') }}" alt="Ilustrasi perlindungan ketiga" class="h-full w-full object-contain" loading="lazy">
            </div>
        </div>
    </div>
</section>

<!-- Who Is Built For Section (Interactive Tabs) -->
<section
    class="bg-black py-24 text-white"
    x-data="{
        activeTab: 'student',
        tabs: {
            student: {
                label: 'SISWA DAN PELAPOR',
                title: 'Ruang aman bagi siswa untuk bersuara.',
                description: 'Memungkinkan siswa menyampaikan laporan kendala fasilitas, masalah akademik, atau isu kedisiplinan secara langsung dengan jaminan kerahasiaan penuh melalui opsi anonim.',
                image: '{{ asset('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcStYFa6CYZcguWPshTf_lbEM1i5j6e_GG37H0qUakaPzXgswXW7C_caWow&s=10') }}'
            },
            counselor: {
                label: 'BK DAN GURU PIKET',
                title: 'Verifikasi kilat dan disposisi tepat sasaran.',
                description: 'Guru Piket dan Tim BK dapat memilah laporan masuk dengan cepat, menolak aduan palsu, atau mendisposisikannya ke unit penanganan terkait tanpa birokrasi yang rumit.',
                image: '{{ asset('https://www.kantorkita.co.id/wp-content/uploads/2025/02/Screenshot_2-2-1080x675.png') }}'
            },
            facilities: {
                label: 'TIM SARANA PRASARANA',
                title: 'Eksekusi lapangan yang transparan.',
                description: 'Tim teknis dan sarana prasarana menerima tiket tugas secara terstruktur, memperbarui status perbaikan secara berkala, dan mengunggah foto bukti penyelesaian.',
                image: '{{ asset('https://static.republika.co.id/uploads/images/inpicture_slide/141028154802-360.jpg') }}'
            }
        }
    }"
>
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">  
        <!-- Header Title & Subtitle -->
        <div class="text-center space-y-3 mb-16">
            <h2 class="text-4xl sm:text-5xl font-black tracking-tight text-white">
                Siapa Saja yang Menggunakan Platform Ini?
            </h2>
            <p class="text-sm sm:text-base text-zinc-400 max-w-xl mx-auto">
                Platform terpadu yang dirancang untuk mendukung siswa, guru, dan pengambil keputusan agar penanganan laporan berjalan cepat, transparan, dan akurat.
            </p>
        </div>

        <!-- Main Box Container -->
        <div class="border border-zinc-800 rounded-2xl overflow-hidden bg-zinc-950 shadow-2xl">
            
            <!-- Top Tabs Bar -->
            <div class="home-tabs grid grid-cols-1 md:grid-cols-3 border-b border-zinc-800 text-xs font-bold uppercase tracking-wider text-center">
                <template x-for="(tab, key) in tabs" :key="key">
                    <button
                        @click="activeTab = key"
                        :class="activeTab === key ? 'bg-[#f4efe6] text-black border-b-2 border-white' : 'bg-black text-zinc-400 hover:text-white hover:bg-zinc-900'"
                        class="py-4 px-4 border-r border-zinc-800 transition-all"
                        x-text="tab.label"
                    ></button>
                </template>
            </div>

            <!-- Content Area (Split Grid: Left Text, Right Image/Visual) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 lg:h-[560px]">
                
                <!-- Left Side: Dynamic Text Based on Active Tab -->
                <div class="p-8 sm:p-12 flex flex-col justify-start space-y-6">
                    <div class="space-y-4">
                        <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight" x-text="tabs[activeTab].title"></h3>
                        <p class="text-sm sm:text-base text-zinc-400 leading-relaxed" x-text="tabs[activeTab].description"></p>
                    </div>
                </div>

                <!-- Right Side: Image Display Area -->
                <div class="relative h-[380px] lg:h-full bg-zinc-900 border-t lg:border-t-0 lg:border-l border-zinc-800 overflow-hidden">
                    <img :src="tabs[activeTab].image" :alt="tabs[activeTab].title" class="block w-full h-full object-cover opacity-90">
                </div>

            </div>
        </div>

    </div>
</section>

<!-- Impact Stories Section -->
<section
    class="overflow-hidden bg-[#f4efe6] py-20 text-zinc-950 sm:py-24"
    x-data="{
        stories: [
            { stat: 'Pembangunan', caption: 'rata-rata waktu respons awal', image: '{{ asset('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRvodCwCcGxCXU2fyYdtVEAv2GKI3m1Tn6sB89SxHOYqIjRLG55OZqQuYI&s=10') }}', tags: ['AMAN', 'RAPIH'] },
            { stat: 'Perbaikan', caption: 'proses penanganan transparan', image: '{{ asset('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTVfMjlbtQEqtPBCnWdcsgp6LvYoS3dwfUP53Tm-x87RLeOiX1HTQwcgaF4&s=10') }}', tags: ['CEPAT', 'PERAWATAN'] },
            { stat: 'Keamanan', caption: 'laporan tercatat dalam satu sistem', image: '{{ asset('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQjO2pyyOMJBCbXhl5q6J7kNjuYziG7gEq3mszMyhwS1zeTehiWjBskTUrx&s=10') }}', tags: ['TERPANTAU', 'TERJAMIN'] },
            { stat: 'Tidur di Kelas', caption: 'semua aspirasi terpusat', image: '{{ asset('https://media.suara.com/pictures/970x544/2019/08/16/67164-aksi-kocak-guru-yang-temukan-murid-tidur-di-kelas-facebook-s-h-i-t-p-o-s-t-b-a-r-b-a-r.jpg') }}', tags: ['KEDISIPLINAN', 'SIKAP'] },
            { stat: 'Kasus Rokok', caption: 'status laporan mudah dilacak', image: '{{ asset('https://akcdn.detik.net.id/community/media/visual/2017/07/27/4a7b1abb-7658-4471-82be-4500bf6fbfdd_11.jpeg?w=250&q=80') }}', tags: ['PELANGGARAN', 'PERATURAN'] },
            { stat: 'Parkir Liar', caption: 'akses pengaduan kapan saja', image: '{{ asset('https://sulbarpedia.com/wp-content/uploads/2026/04/IMG-20260424-WA0057-scaled.jpg') }}', tags: ['SIAP', 'PELANGGARAN'] },
            { stat: 'Renovasi', caption: 'identitas pelapor terlindungi', image: '{{ asset('https://awsimages.detik.net.id/community/media/visual/2015/08/19/efff3a8d-50a9-4e86-8cf6-9a3d22377609_169.jpg?w=1200') }}', tags: ['CEPAT', 'TANGGAP'] },
            { stat: 'Kebersihan', caption: 'laporan segera diteruskan', image: '{{ asset('https://assets-a1.kompasiana.com/items/album/2024/01/28/5e535d688a30e-65b5d4c1c57afb2e66306037.jpg') }}', tags: ['INDAH', 'NYAMAN'] },
        ],
        testimonial: {
            quote: 'Sekarang setiap laporan memiliki jalur yang jelas. Siswa dapat bersuara, dan sekolah dapat merespons dengan lebih cepat serta bertanggung jawab.',
            author: 'Tim Pengelola Pengaduan Sekolah',
            role: 'Layanan Aspirasi Terpadu'
        }
    }"
>
    <div class="mx-auto max-w-[90rem]">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
            <p class="home-tabs text-[10px] font-medium uppercase tracking-[0.24em] text-red-500">
                <span class="mr-2 inline-block h-1.5 w-1.5 border border-red-500 align-middle"></span>
                Cerita dampak · umpan balik nyata
            </p>
            <h2 class="mt-6 text-4xl font-normal tracking-tight sm:text-5xl">
                Pengaduan yang bergerak menuju perubahan
            </h2>
        </div>

        <div class="impact-marquee mt-14 overflow-hidden pb-3" aria-label="Cerita dampak layanan pengaduan">
            <div class="impact-marquee-track flex w-max gap-2">
                <template x-for="copy in 2" :key="copy">
                    <div class="flex shrink-0 gap-2" :aria-hidden="copy === 2">
                        <template x-for="(story, index) in stories" :key="`${copy}-${index}`">
                            <article class="relative h-[380px] w-[78vw] shrink-0 overflow-hidden bg-zinc-900 text-white sm:h-[420px] sm:w-[300px] lg:w-[320px]">
                                <img :src="story.image" :alt="story.caption" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/70"></div>
                                <div class="relative flex h-full flex-col justify-between p-5 sm:p-6">
                                    <div>
                                        <strong class="block text-3xl font-normal tracking-tight sm:text-4xl" x-text="story.stat"></strong>
                                        <span class="mt-1 block max-w-[13rem] text-xs text-white/80" x-text="story.caption"></span>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="tag in story.tags" :key="`${copy}-${index}-${tag}`">
                                            <span class="bg-black/40 px-2.5 py-1 text-[9px] font-medium uppercase tracking-wider text-white/90" x-text="tag"></span>
                                        </template>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <div class="mx-auto mt-14 max-w-2xl px-4 text-center sm:px-6">
            <div class="flex justify-center gap-1 text-lg text-zinc-400" aria-label="Lima dari lima bintang">
                <template x-for="star in 5" :key="star">
                    <span aria-hidden="true">&#9733;</span>
                </template>
            </div>
            <blockquote class="mt-5 text-2xl leading-tight tracking-tight sm:text-3xl" x-text="`“${testimonial.quote}”`"></blockquote>
            <p class="mt-7 text-xs font-medium uppercase tracking-wider text-zinc-500">
                <span x-text="testimonial.author"></span>
                <span class="mx-1">·</span>
                <span x-text="testimonial.role"></span>
            </p>
        </div>
    </div>
</section>


@if($recentComplaints->isNotEmpty())
<section class="py-24 bg-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-16">
            <h2 class="text-sm font-medium text-zinc-500 uppercase tracking-widest mb-2">Public Feed</h2>
            <p class="text-3xl font-normal text-white">Laporan Terbaru</p>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-px bg-zinc-900 border border-zinc-900">
            @foreach($recentComplaints as $complaint)
                <a href="{{ route('pengaduan.show', $complaint->ticket_code) }}" 
                   class="group bg-black p-8 hover:bg-zinc-950 transition-colors duration-300 block">
                    
                    <div class="flex items-start justify-between mb-6">
                        <span class="font-mono text-xs text-zinc-600">{{ $complaint->ticket_code }}</span>
                        <div class="text-[10px] uppercase tracking-widest text-zinc-500">{{ $complaint->status }}</div>
                    </div>

                    <h3 class="text-lg font-medium text-white mb-3 group-hover:text-zinc-300 transition-colors">
                        {{ $complaint->title }}
                    </h3>
                    
                    <p class="text-sm text-zinc-500 line-clamp-2 leading-relaxed">
                        {{ $complaint->description }}
                    </p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif


</div>
@endsection
