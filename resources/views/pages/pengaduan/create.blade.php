@extends('layouts.app')

@section('title', 'Buat Formulir Pengaduan Sekolah')

@section('content')
<div class="py-12 bg-black min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-blue-600 transition mb-3">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Beranda
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                Formulir Pengaduan & Aspirasi Sekolah
            </h1>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                Sampaikan laporan kerusakan fasilitas, mediasi konseling, atau kendala sekolah secara akurat.
            </p>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-10 shadow-xl"
             x-data="{
                isAnonymous: {{ old('is_anonymous') ? 'true' : (Auth::check() ? 'false' : 'false') }},
                priority: '{{ old('priority', 'sedang') }}',
                files: [],
                handleFileSelect(event) {
                    this.files = Array.from(event.target.files);
                }
             }">

            <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Section 1: Identitas Pelapor -->
                <div class="space-y-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-blue-600/10 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                            Identitas Pelapor
                        </h2>
                        
                        <!-- Toggle Anonim -->
                        <label class="flex items-center gap-2.5 cursor-pointer bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                            <input type="checkbox" name="is_anonymous" value="1" x-model="isAnonymous" class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                                <i data-lucide="eye-off" class="w-3.5 h-3.5 text-amber-500"></i> Mode Anonim (Rahasiakan Nama)
                            </span>
                        </label>
                    </div>

                    @if(Auth::check())
                        <div class="bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 rounded-2xl p-4 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</div>
                                    <div class="text-slate-500">{{ Auth::user()->email }} &bull; {{ Auth::user()->department ?? 'Siswa' }}</div>
                                </div>
                            </div>
                            <span x-show="isAnonymous" class="text-amber-600 dark:text-amber-400 font-bold bg-amber-100 dark:bg-amber-900/50 px-2 py-1 rounded-md text-[11px]">
                                Identitas akan disamarkan
                            </span>
                        </div>
                    @else
                        <!-- Form Guest / Belum Login -->
                        <div x-show="!isAnonymous" class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-transition>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Nama Lengkap Pelapor *</label>
                                <input type="text" name="reporter_name" value="{{ old('reporter_name') }}" placeholder="Contoh: Ahmad Fauzan" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">NISN / NIP (Opsional)</label>
                                <input type="text" name="reporter_nisn" value="{{ old('reporter_nisn') }}" placeholder="Contoh: 0068945123" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kelas / Rombel</label>
                                <input type="text" name="reporter_class" value="{{ old('reporter_class') }}" placeholder="Contoh: XI MIPA 2" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">No. WhatsApp / Telepon Aktif</label>
                                <input type="text" name="reporter_phone" value="{{ old('reporter_phone') }}" placeholder="Contoh: 08123456789" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>

                        <div x-show="isAnonymous" class="p-4 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 rounded-2xl text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
                            <i data-lucide="shield-alert" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
                            <div>
                                <div class="font-bold">Laporan Dikirim Secara Anonim</div>
                                <div>Nama dan kontak Anda tidak akan dicatat atau ditampilkan kepada publik. Anda tetap dapat memantau status menggunakan <strong>Kode Tiket</strong> yang diterbitkan setelah submit.</div>
                            </div>
                            <input type="hidden" name="reporter_name" value="Siswa Anonim" x-bind:disabled="!isAnonymous">
                        </div>
                    @endif
                </div>

                <!-- Section 2: Detail Pengaduan -->
                <div class="space-y-5 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-blue-600/10 text-blue-600 flex items-center justify-center text-xs font-bold">2</span>
                        Detail Kasus / Fasilitas yang Dilaporkan
                    </h2>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Kategori Pengaduan *</label>
                        <select name="category_id" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Pilih Kategori Masalah --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Judul Pengaduan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Judul Pengaduan Singkat & Jelas *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Lampu Koridor Ruang Guru Mati Total" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Lokasi Kejadian -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Lokasi / Ruangan Spesifik</label>
                            <input type="text" name="location" value="{{ old('location') }}" placeholder="Contoh: Gedung C Lantai 2, Ruang Lab Fisika" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- Prioritas / Urgensi -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tingkat Urgensi Masalah *</label>
                            <select name="priority" x-model="priority" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="rendah">Rendah (Dapat menunggu beberapa hari)</option>
                                <option value="sedang">Sedang (Perlu diperbaiki dalam minggu ini)</option>
                                <option value="tinggi">Tinggi (Mengganggu kegiatan belajar mengajar)</option>
                                <option value="darurat">Darurat (Berbahaya / Bullying / Keselamatan Fisik)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Isi Deskripsi Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Deskripsi Kronologi / Rincian Kerusakan *</label>
                        <textarea name="description" rows="5" required placeholder="Jelaskan secara runtut apa yang terjadi, kapan waktu kejadian, atau bagian mana dari alat/fasilitas yang rusak..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none leading-relaxed">{{ old('description') }}</textarea>
                    </div>
                </div>

                <!-- Section 3: Lampiran Bukti Foto / Dokumen -->
                <div class="space-y-4">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-blue-600/10 text-blue-600 flex items-center justify-center text-xs font-bold">3</span>
                        Lampiran Bukti Foto / Dokumen Pendukung
                    </h2>

                    <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl p-6 text-center hover:border-blue-500 transition relative">
                        <input type="file" 
                               name="attachments[]" 
                               multiple 
                               accept="image/*,.pdf,.doc,.docx" 
                               @change="handleFileSelect"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        
                        <div class="space-y-2 pointer-events-none">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                            </div>
                            <div class="text-xs font-bold text-slate-800 dark:text-slate-200">
                                Klik atau seret foto bukti kerusakan ke area ini
                            </div>
                            <p class="text-[11px] text-slate-500">Mendukung format JPG, PNG, WEBP, PDF (Maksimal 5MB per berkas)</p>
                        </div>
                    </div>

                    <!-- Selected Files Preview List -->
                    <template x-if="files.length > 0">
                        <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-3 border border-slate-200 dark:border-slate-700 space-y-1.5">
                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Berkas Terpilih:</div>
                            <template x-for="(file, index) in files" :key="index">
                                <div class="flex items-center justify-between text-xs py-1 px-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                    <span class="truncate font-medium text-slate-700 dark:text-slate-300" x-text="file.name"></span>
                                    <span class="text-[10px] text-slate-400 font-mono" x-text="(file.size / 1024).toFixed(1) + ' KB'"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-xs text-slate-500 flex items-center gap-1.5">
                        <i data-lucide="info" class="w-4 h-4 text-blue-500 shrink-0"></i>
                        <span>Laporan akan diteruskan ke Guru Piket untuk diperiksa.</span>
                    </div>

                    <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold px-8 py-3.5 rounded-2xl shadow-xl shadow-blue-500/25 transition transform active:scale-95 text-xs">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span>Kirimkan Pengaduan</span>
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
