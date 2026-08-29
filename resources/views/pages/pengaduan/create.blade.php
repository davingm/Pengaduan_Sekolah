@extends('layouts.app')

@section('title', 'Buat Formulir Pengaduan Sekolah')

@section('content')
@php
    $initialStep = 1;
    if ($errors->any()) {
        if ($errors->hasAny(['reporter_name', 'reporter_nisn', 'reporter_class', 'reporter_phone', 'reporter_email'])) {
            $initialStep = 1;
        } elseif ($errors->hasAny(['category_id', 'title', 'description', 'location', 'priority'])) {
            $initialStep = 2;
        } elseif ($errors->hasAny(['attachments', 'attachments.*'])) {
            $initialStep = 3;
        }
    }
@endphp

<div class="min-h-screen bg-neutral-950 py-10 sm:py-14 lg:py-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8"
         x-data="pengaduanWizard()"
         x-init="init()">

        <!-- Header -->
        <div class="mb-10 lg:mb-16 lg:max-w-2xl">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold mb-6 text-neutral-500 hover:text-white transition">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Beranda
            </a>
            <div class="text-[11px] font-bold font-mono tracking-wider text-neutral-500 mb-2">PENGADUAN &middot; 01&ndash;04</div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white">
                Formulir Pengaduan &amp; Aspirasi Sekolah
            </h1>
            <p class="text-[13px] lg:text-sm text-neutral-400 mt-3 leading-relaxed">
                Laporkan kerusakan fasilitas, kebutuhan mediasi, atau kendala sekolah lainnya. Setiap langkah divalidasi otomatis sebelum lanjut ke tahap berikutnya.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-8 p-4 bg-red-950/40 border border-red-800 text-red-200 text-xs">
                <div class="flex items-center gap-2 font-bold mb-2 text-red-400">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    <span>Terdapat data yang belum sesuai pada formulir:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-neutral-300">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="lg:grid lg:grid-cols-[240px_1fr] lg:gap-16">

            <!-- Desktop: vertical stepper rail -->
            <div class="hidden lg:block">
                <div class="sticky top-14 space-y-1">
                    <template x-for="(step, i) in steps" :key="i">
                        <button type="button" @click="goTo(i + 1)"
                                class="w-full flex items-start gap-3 px-3 py-3 text-left border transition-all cursor-pointer"
                                :class="currentStep === i + 1 ? 'border-neutral-800 bg-neutral-900 shadow-sm' : 'border-transparent hover:bg-neutral-900/60'">
                            <span class="w-7 h-7 rounded-full border flex items-center justify-center text-[11px] font-bold font-mono flex-shrink-0 transition-colors"
                                  :class="hasStepError(i + 1) ? 'bg-red-500/20 border-red-500 text-red-400' : (i + 1 < currentStep ? 'bg-white border-white text-neutral-950' : (currentStep === i + 1 ? 'border-white text-white' : 'border-neutral-700 text-neutral-500'))">
                                <i data-lucide="check" class="w-3.5 h-3.5" x-show="i + 1 < currentStep && !hasStepError(i + 1)" x-cloak></i>
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5" x-show="hasStepError(i + 1)" x-cloak></i>
                                <span x-show="i + 1 >= currentStep && !hasStepError(i + 1)" x-text="String(i + 1).padStart(2, '0')"></span>
                            </span>
                            <span class="pt-1.5 flex-1">
                                <span class="block text-[13px] font-semibold" :class="hasStepError(i + 1) ? 'text-red-400' : (currentStep === i + 1 ? 'text-white' : 'text-neutral-500')" x-text="step"></span>
                                <span class="block text-[10.5px] text-neutral-600 mt-0.5" x-text="getStepSubtitle(i + 1)"></span>
                            </span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Mobile: horizontal stepper -->
            <div class="lg:hidden mb-8">
                <div class="flex items-center mb-2" aria-label="Langkah pengisian formulir">
                    <template x-for="(step, i) in steps" :key="i">
                        <div class="flex items-center flex-1 last:flex-none">
                            <button type="button" @click="goTo(i + 1)"
                                    class="w-8 h-8 rounded-full border flex items-center justify-center text-xs font-bold font-mono flex-shrink-0 transition-colors cursor-pointer"
                                    :class="hasStepError(i + 1) ? 'bg-red-500/20 border-red-500 text-red-400' : (i + 1 < currentStep ? 'bg-white border-white text-neutral-950' : (currentStep === i + 1 ? 'border-white text-white' : 'border-neutral-700 text-neutral-500'))">
                                <i data-lucide="check" class="w-3.5 h-3.5" x-show="i + 1 < currentStep && !hasStepError(i + 1)" x-cloak></i>
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5" x-show="hasStepError(i + 1)" x-cloak></i>
                                <span x-show="i + 1 >= currentStep && !hasStepError(i + 1)" x-text="String(i + 1).padStart(2, '0')"></span>
                            </button>
                            <div class="flex-1 h-px mx-1 transition-colors" x-show="i < steps.length - 1" :class="i + 1 < currentStep ? 'bg-white' : 'bg-neutral-800'"></div>
                        </div>
                    </template>
                </div>
                <div class="text-[12px] font-semibold text-white">
                    <span class="font-medium text-neutral-500" x-text="`Langkah ${currentStep} dari ${steps.length}`"></span>
                    <span class="text-neutral-600"> &middot; </span>
                    <span x-text="steps[currentStep - 1]"></span>
                </div>
            </div>

            <!-- Form card -->
            <div class="bg-neutral-900 border border-neutral-800 p-6 sm:p-8 lg:p-10">
                <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" @submit="onSubmit" x-ref="form">
                    @csrf

                    <!-- STEP 1 : Identitas Pelapor -->
                    <div x-show="currentStep === 1" x-cloak
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-[15px] lg:text-base font-bold text-white">Identitas Pelapor</h2>
                                <p class="text-[12px] mt-0.5 text-neutral-400">Data ini membantu pihak sekolah menindaklanjuti laporan Anda.</p>
                            </div>
                            <div class="flex items-center gap-2.5 flex-shrink-0 pt-0.5">
                                <span class="text-[11.5px] font-semibold text-white">Anonim</span>
                                <input type="checkbox" name="is_anonymous" value="1" x-model="isAnonymous" @change="onAnonymousToggle()" class="hidden">
                                <button type="button" @click="isAnonymous = !isAnonymous; onAnonymousToggle()"
                                        class="relative w-10 h-6 rounded-full transition-colors flex-shrink-0"
                                        :class="isAnonymous ? 'bg-white' : 'bg-neutral-700'">
                                    <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full transition-transform"
                                          :class="isAnonymous ? 'translate-x-4 bg-neutral-950' : 'translate-x-0 bg-white'"></span>
                                </button>
                            </div>
                        </div>

                        @if(Auth::check())
                            <div class="p-4 flex items-center justify-between text-xs border border-neutral-800 bg-neutral-950">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-white text-neutral-950 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-white">{{ Auth::user()->name }}</div>
                                        <div class="text-neutral-500">{{ Auth::user()->email }} &bull; {{ Auth::user()->department ?? 'Siswa' }}</div>
                                    </div>
                                </div>
                                <span x-show="isAnonymous" x-cloak class="font-bold px-2.5 py-1 text-[10.5px] bg-neutral-800 text-white">
                                    Identitas disamarkan
                                </span>
                            </div>
                        @else
                            <div x-show="!isAnonymous" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-[11.5px] font-semibold text-white mb-1.5">
                                        Nama lengkap pelapor <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" name="reporter_name" x-model="form.reporter_name" @input="clearFieldError('reporter_name')"
                                           placeholder="Contoh: Ahmad Fauzan"
                                           class="w-full bg-neutral-950 border px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none transition"
                                           :class="fieldErrors.reporter_name ? 'border-red-500 bg-red-950/10 focus:border-red-500' : 'border-neutral-800 focus:border-white focus:bg-neutral-900'">
                                    <p x-show="fieldErrors.reporter_name" x-cloak class="text-[11.5px] font-medium text-red-400 mt-1 flex items-center gap-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        <span x-text="fieldErrors.reporter_name"></span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-[11.5px] font-semibold text-white mb-1.5">NISN / NIP (opsional)</label>
                                    <input type="text" name="reporter_nisn" x-model="form.reporter_nisn" @input="clearFieldError('reporter_nisn')"
                                           placeholder="0068945123"
                                           class="w-full bg-neutral-950 border border-neutral-800 px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none focus:border-white focus:bg-neutral-900 transition">
                                </div>
                                <div>
                                    <label class="block text-[11.5px] font-semibold text-white mb-1.5">Kelas / Rombel / Jabatan (opsional)</label>
                                    <input type="text" name="reporter_class" x-model="form.reporter_class" @input="clearFieldError('reporter_class')"
                                           placeholder="XI MIPA 2"
                                           class="w-full bg-neutral-950 border border-neutral-800 px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none focus:border-white focus:bg-neutral-900 transition">
                                </div>
                                <div>
                                    <label class="block text-[11.5px] font-semibold text-white mb-1.5">No. WhatsApp / Telepon aktif (opsional)</label>
                                    <input type="tel" name="reporter_phone" x-model="form.reporter_phone" @input="clearFieldError('reporter_phone')"
                                           placeholder="08123456789"
                                           class="w-full bg-neutral-950 border px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none transition"
                                           :class="fieldErrors.reporter_phone ? 'border-red-500 bg-red-950/10 focus:border-red-500' : 'border-neutral-800 focus:border-white focus:bg-neutral-900'">
                                    <p x-show="fieldErrors.reporter_phone" x-cloak class="text-[11.5px] font-medium text-red-400 mt-1 flex items-center gap-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        <span x-text="fieldErrors.reporter_phone"></span>
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-[11.5px] font-semibold text-white mb-1.5">Email aktif (opsional)</label>
                                    <input type="email" name="reporter_email" x-model="form.reporter_email" @input="clearFieldError('reporter_email')"
                                           placeholder="nama@email.com"
                                           class="w-full bg-neutral-950 border px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none transition"
                                           :class="fieldErrors.reporter_email ? 'border-red-500 bg-red-950/10 focus:border-red-500' : 'border-neutral-800 focus:border-white focus:bg-neutral-900'">
                                    <p x-show="fieldErrors.reporter_email" x-cloak class="text-[11.5px] font-medium text-red-400 mt-1 flex items-center gap-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        <span x-text="fieldErrors.reporter_email"></span>
                                    </p>
                                </div>
                            </div>

                            <div x-show="isAnonymous" x-cloak x-transition class="p-4 text-xs flex items-start gap-2.5 border border-neutral-800 bg-neutral-950">
                                <i data-lucide="shield-check" class="w-4 h-4 flex-shrink-0 mt-0.5 text-white"></i>
                                <div class="text-neutral-400">
                                    <div class="font-bold mb-0.5 text-white">Laporan dikirim secara anonim</div>
                                    Nama dan kontak Anda tidak dicatat maupun ditampilkan ke publik. Status laporan tetap bisa dipantau memakai <strong class="text-white">kode tiket</strong> yang diterbitkan setelah formulir dikirim.
                                </div>
                                <input type="hidden" name="reporter_name" value="Siswa Anonim">
                            </div>
                        @endif

                        <div x-show="stepErrors[1]" x-cloak class="p-3 bg-red-950/30 border border-red-800/80 text-red-300 text-xs flex items-center gap-2">
                            <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0 text-red-400"></i>
                            <span x-text="stepErrors[1]"></span>
                        </div>
                    </div>

                    <!-- STEP 2 : Detail Pengaduan -->
                    <div x-show="currentStep === 2" x-cloak
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-5">
                        <div>
                            <h2 class="text-[15px] lg:text-base font-bold text-white">Detail Kasus atau Fasilitas</h2>
                            <p class="text-[12px] mt-0.5 text-neutral-400">Semakin spesifik, semakin cepat ditindaklanjuti oleh petugas sekolah.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11.5px] font-semibold text-white mb-1.5">
                                    Kategori pengaduan <span class="text-red-400">*</span>
                                </label>
                                <div class="relative">
                                    <select name="category_id" x-model="form.category_id" @change="clearFieldError('category_id')"
                                            class="appearance-none w-full bg-neutral-950 border px-3.5 py-2.5 pr-9 text-sm text-white focus:outline-none transition cursor-pointer"
                                            :class="fieldErrors.category_id ? 'border-red-500 bg-red-950/10 focus:border-red-500' : 'border-neutral-800 focus:border-white'">
                                        <option value="">Pilih kategori masalah</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-neutral-500 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                </div>
                                <p x-show="fieldErrors.category_id" x-cloak class="text-[11.5px] font-medium text-red-400 mt-1 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                    <span x-text="fieldErrors.category_id"></span>
                                </p>
                            </div>
                            <div>
                                <label class="block text-[11.5px] font-semibold text-white mb-1.5">Lokasi / ruangan spesifik (opsional)</label>
                                <input type="text" name="location" x-model="form.location" @input="clearFieldError('location')"
                                       placeholder="Contoh: Gedung C lantai 2, Lab Fisika"
                                       class="w-full bg-neutral-950 border border-neutral-800 px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none focus:border-white focus:bg-neutral-900 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11.5px] font-semibold text-white mb-1.5">
                                Judul pengaduan <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="title" x-model="form.title" @input="clearFieldError('title')"
                                   placeholder="Contoh: Lampu koridor ruang guru mati total"
                                   class="w-full bg-neutral-950 border px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none transition"
                                   :class="fieldErrors.title ? 'border-red-500 bg-red-950/10 focus:border-red-500' : 'border-neutral-800 focus:border-white focus:bg-neutral-900'">
                            <p x-show="fieldErrors.title" x-cloak class="text-[11.5px] font-medium text-red-400 mt-1 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                <span x-text="fieldErrors.title"></span>
                            </p>
                        </div>

                        <div>
                            <label class="block text-[11.5px] font-semibold text-white mb-1.5">
                                Tingkat urgensi <span class="text-red-400">*</span>
                            </label>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
                                <template x-for="opt in priorityOptions" :key="opt.value">
                                    <label class="border p-3 cursor-pointer transition-colors flex items-start gap-2.5"
                                           :class="priority === opt.value ? 'border-white bg-neutral-800' : 'border-neutral-800 hover:border-neutral-700'">
                                        <input type="radio" name="priority" class="hidden" :value="opt.value" x-model="priority" @change="clearFieldError('priority')">
                                        <span class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" :class="opt.dot"></span>
                                        <span>
                                            <span class="block text-xs font-bold text-white" x-text="opt.label"></span>
                                            <span class="block text-[11px] text-neutral-500 mt-0.5" x-text="opt.hint"></span>
                                        </span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-[11.5px] font-semibold text-white">
                                    Kronologi / rincian kerusakan <span class="text-red-400">*</span>
                                </label>
                                <span class="text-[10.5px] text-neutral-500 font-mono"
                                      :class="form.description.trim().length >= 10 ? 'text-emerald-400' : 'text-neutral-500'"
                                      x-text="`${form.description.trim().length} / min 10 karakter`"></span>
                            </div>
                            <textarea name="description" x-model="form.description" @input="clearFieldError('description')" rows="5"
                                      placeholder="Jelaskan apa yang terjadi, kapan waktunya, dan bagian mana yang bermasalah (minimal 10 karakter)..."
                                      class="w-full bg-neutral-950 border p-3.5 text-sm text-white placeholder-neutral-600 focus:outline-none transition leading-relaxed"
                                      :class="fieldErrors.description ? 'border-red-500 bg-red-950/10 focus:border-red-500' : 'border-neutral-800 focus:border-white focus:bg-neutral-900'"></textarea>
                            <p x-show="fieldErrors.description" x-cloak class="text-[11.5px] font-medium text-red-400 mt-1 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                <span x-text="fieldErrors.description"></span>
                            </p>
                        </div>

                        <div x-show="stepErrors[2]" x-cloak class="p-3 bg-red-950/30 border border-red-800/80 text-red-300 text-xs flex items-center gap-2">
                            <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0 text-red-400"></i>
                            <span x-text="stepErrors[2]"></span>
                        </div>
                    </div>

                    <!-- STEP 3 : Bukti -->
                    <div x-show="currentStep === 3" x-cloak
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-5">
                        <div>
                            <h2 class="text-[15px] lg:text-base font-bold text-white">Bukti Foto atau Dokumen</h2>
                            <p class="text-[12px] mt-0.5 text-neutral-400">Opsional, tapi mempercepat verifikasi. Ambil langsung dari kamera atau unggah berkas.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" @click="openCamera()"
                                    class="border border-neutral-700 text-white font-semibold px-5 py-3.5 text-[12.5px] hover:bg-neutral-800/60 hover:border-neutral-600 transition inline-flex items-center justify-center gap-2">
                                <i data-lucide="camera" class="w-4 h-4"></i> Ambil foto
                            </button>
                            <button type="button" @click="$refs.fileInput.click()"
                                    class="border border-neutral-700 text-white font-semibold px-5 py-3.5 text-[12.5px] hover:bg-neutral-800/60 hover:border-neutral-600 transition inline-flex items-center justify-center gap-2">
                                <i data-lucide="upload" class="w-4 h-4"></i> Unggah berkas
                            </button>
                        </div>

                        <div class="border-2 border-dashed px-6 py-8 text-center relative transition-colors"
                             :class="dragActive ? 'border-white bg-neutral-950' : 'border-neutral-700'"
                             @dragover.prevent="dragActive = true"
                             @dragleave.prevent="dragActive = false"
                             @drop.prevent="dragActive = false; handleFileSelection($event.dataTransfer.files)">
                            <input type="file" x-ref="fileInput" multiple accept="image/jpeg,image/png,image/webp,application/pdf,.doc,.docx" @change="handleFileSelection($event.target.files); $event.target.value = ''" class="hidden">
                            <div class="w-10 h-10 mx-auto flex items-center justify-center mb-2 border border-neutral-800 bg-neutral-950">
                                <i data-lucide="upload-cloud" class="w-5 h-5 text-neutral-500"></i>
                            </div>
                            <div class="text-[12px] font-semibold text-white">Seret berkas ke sini, atau klik tombol unggah di atas</div>
                            <p class="text-[10.5px] mt-1 text-neutral-500">JPG, PNG, WEBP, PDF, DOC, DOCX &middot; Maksimal 5MB per berkas</p>
                        </div>

                        <!-- Step 3 File Error Alert -->
                        <div x-show="fileErrors.length > 0" x-cloak class="p-3.5 bg-red-950/40 border border-red-800 text-red-300 text-xs space-y-1">
                            <div class="flex items-center gap-2 font-bold text-red-400">
                                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                <span>Beberapa berkas tidak dapat ditambahkan:</span>
                            </div>
                            <ul class="list-disc list-inside text-[11.5px] space-y-0.5">
                                <template x-for="(err, idx) in fileErrors" :key="idx">
                                    <li x-text="err"></li>
                                </template>
                            </ul>
                        </div>

                        <!-- Hidden real input synced for submission -->
                        <input type="file" name="attachments[]" x-ref="hiddenSubmitInput" multiple class="hidden">

                        <template x-if="files.length > 0">
                            <div>
                                <div class="text-[11.5px] font-semibold text-white mb-2">Berkas terpilih (<span x-text="files.length"></span> berkas):</div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                                    <template x-for="(file, index) in files" :key="file._id">
                                        <div class="relative group border border-neutral-800 bg-neutral-950 p-2 flex flex-col justify-between">
                                            <div class="aspect-square bg-neutral-900 flex items-center justify-center overflow-hidden mb-2 relative">
                                                <template x-if="file.isImage">
                                                    <img class="w-full h-full object-cover" :src="file._preview" :alt="file.name">
                                                </template>
                                                <template x-if="!file.isImage">
                                                    <div class="flex flex-col items-center justify-center text-neutral-400 p-2 text-center">
                                                        <i data-lucide="file-text" class="w-8 h-8 mb-1 text-neutral-500"></i>
                                                        <span class="text-[10px] uppercase font-mono" x-text="file.extension"></span>
                                                    </div>
                                                </template>
                                                <button type="button" @click="removeFile(index)"
                                                        class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-black/80 hover:bg-red-600 flex items-center justify-center text-white transition">
                                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </div>
                                            <div class="text-[11px] font-medium text-white truncate" x-text="file.name"></div>
                                            <div class="text-[10px] text-neutral-500" x-text="formatFileSize(file.size)"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div x-show="stepErrors[3]" x-cloak class="p-3 bg-red-950/30 border border-red-800/80 text-red-300 text-xs flex items-center gap-2">
                            <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0 text-red-400"></i>
                            <span x-text="stepErrors[3]"></span>
                        </div>
                    </div>

                    <!-- STEP 4 : Review -->
                    <div x-show="currentStep === 4" x-cloak
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-5">
                        <div>
                            <h2 class="text-[15px] lg:text-base font-bold text-white">Tinjau Sebelum Mengirim</h2>
                            <p class="text-[12px] mt-0.5 text-neutral-400">Pastikan seluruh detail sudah lengkap dan benar. Anda dapat mengklik langkah untuk mengubah.</p>
                        </div>

                        <div class="border border-neutral-800 divide-y divide-neutral-800">
                            <!-- Identitas Pelapor -->
                            <div class="p-4 flex items-start justify-between gap-4">
                                <div>
                                    <span class="text-[11px] font-mono uppercase text-neutral-500 block mb-0.5">01 &middot; Identitas Pelapor</span>
                                    <span class="text-[13px] font-semibold text-white" x-text="isAnonymous ? 'Anonim (Identitas Disamarkan)' : (form.reporter_name || '—')"></span>
                                    <template x-if="!isAnonymous && (form.reporter_phone || form.reporter_class || form.reporter_email || form.reporter_nisn)">
                                        <div class="text-[11.5px] text-neutral-400 mt-1 space-x-2">
                                            <span x-show="form.reporter_class" x-text="'Kelas: ' + form.reporter_class"></span>
                                            <span x-show="form.reporter_nisn" x-text="'NISN: ' + form.reporter_nisn"></span>
                                            <span x-show="form.reporter_phone" x-text="'Tel: ' + form.reporter_phone"></span>
                                            <span x-show="form.reporter_email" x-text="'Email: ' + form.reporter_email"></span>
                                        </div>
                                    </template>
                                </div>
                                <button type="button" @click="goTo(1)" class="text-xs text-neutral-400 hover:text-white underline underline-offset-2 flex-shrink-0 cursor-pointer">
                                    Ubah
                                </button>
                            </div>

                            <!-- Detail Kasus -->
                            <div class="p-4 flex items-start justify-between gap-4">
                                <div class="space-y-2 flex-1">
                                    <span class="text-[11px] font-mono uppercase text-neutral-500 block">02 &middot; Detail Kasus</span>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <span class="text-neutral-500 block text-[11px]">Kategori:</span>
                                            <span class="font-semibold text-white" x-text="getCategoryName(form.category_id) || '—'"></span>
                                        </div>
                                        <div>
                                            <span class="text-neutral-500 block text-[11px]">Lokasi / Ruangan:</span>
                                            <span class="text-neutral-300" x-text="form.location || '—'"></span>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="text-neutral-500 block text-[11px]">Judul Pengaduan:</span>
                                        <span class="font-semibold text-white text-[13px]" x-text="form.title || '—'"></span>
                                    </div>

                                    <div>
                                        <span class="text-neutral-500 block text-[11px]">Tingkat Urgensi:</span>
                                        <span class="inline-flex items-center gap-1.5 text-xs text-white font-semibold mt-0.5">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="priorityOptions.find(p => p.value === priority)?.dot"></span>
                                            <span x-text="priorityOptions.find(p => p.value === priority)?.label"></span>
                                        </span>
                                    </div>

                                    <div>
                                        <span class="text-neutral-500 block text-[11px]">Kronologi:</span>
                                        <p class="text-[12.5px] leading-relaxed text-neutral-200 mt-0.5 whitespace-pre-line" x-text="form.description || '—'"></p>
                                    </div>
                                </div>
                                <button type="button" @click="goTo(2)" class="text-xs text-neutral-400 hover:text-white underline underline-offset-2 flex-shrink-0 cursor-pointer">
                                    Ubah
                                </button>
                            </div>

                            <!-- Lampiran -->
                            <div class="p-4 flex items-start justify-between gap-4">
                                <div>
                                    <span class="text-[11px] font-mono uppercase text-neutral-500 block mb-0.5">03 &middot; Lampiran Bukti</span>
                                    <span class="text-[12.5px] font-semibold text-white" x-text="files.length > 0 ? (files.length + ' berkas terlampir') : 'Tidak ada lampiran (opsional)'"></span>
                                    <template x-if="files.length > 0">
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            <template x-for="f in files" :key="f._id">
                                                <span class="text-[11px] bg-neutral-950 border border-neutral-800 px-2 py-0.5 text-neutral-300 rounded" x-text="f.name"></span>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                                <button type="button" @click="goTo(3)" class="text-xs text-neutral-400 hover:text-white underline underline-offset-2 flex-shrink-0 cursor-pointer">
                                    Ubah
                                </button>
                            </div>
                        </div>

                        <div class="p-3.5 text-[11.5px] flex items-start gap-2 bg-neutral-950 border border-neutral-800 text-neutral-400">
                            <i data-lucide="info" class="w-4 h-4 flex-shrink-0 mt-0.5 text-white"></i>
                            <div>
                                Laporan akan langsung diteruskan ke sistem untuk diverifikasi oleh Guru Piket. Anda akan mendapatkan <strong class="text-white">Kode Tiket</strong> untuk melacak perkembangan status.
                            </div>
                        </div>
                    </div>

                    <!-- Nav -->
                    <div class="flex items-center justify-between gap-3 mt-8 pt-6 border-t border-neutral-800">
                        <button type="button" @click="prev()" x-show="currentStep > 1" x-cloak
                                class="border border-neutral-700 text-white font-semibold px-5 py-3 text-[12.5px] hover:bg-neutral-800/60 hover:border-neutral-600 transition inline-flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali
                        </button>
                        <div x-show="currentStep === 1" class="hidden sm:block"></div>

                        <button type="button" @click="next()" x-show="currentStep < steps.length" x-cloak
                                class="bg-white text-neutral-950 font-semibold px-6 py-3 text-[12.5px] hover:bg-neutral-200 active:scale-[.98] transition inline-flex items-center gap-1.5 ml-auto cursor-pointer">
                            Lanjut <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </button>
                        <button type="submit" x-show="currentStep === steps.length" x-cloak
                                class="bg-white text-neutral-950 font-semibold px-6 py-3 text-[12.5px] hover:bg-neutral-200 active:scale-[.98] transition inline-flex items-center gap-1.5 ml-auto cursor-pointer">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i> Kirim Pengaduan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Camera Modal -->
        <div x-show="cameraOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/95"
             x-transition.opacity>
            <div class="w-full h-full sm:h-auto sm:max-w-md flex flex-col">
                <div class="flex items-center justify-between px-5 py-4">
                    <span class="text-white text-[12.5px] font-semibold">Ambil foto bukti</span>
                    <button type="button" @click="closeCamera()" class="w-8 h-8 rounded-full flex items-center justify-center bg-white/10 hover:bg-white/20 transition">
                        <i data-lucide="x" class="w-4 h-4 text-white"></i>
                    </button>
                </div>

                <div class="relative flex-1 sm:flex-none sm:aspect-[3/4] overflow-hidden mx-4 bg-black">
                    <video x-ref="video" autoplay playsinline muted class="w-full h-full object-cover"></video>
                    <div class="absolute top-4 left-4 w-6 h-6 border-t-2 border-l-2 border-white/90"></div>
                    <div class="absolute top-4 right-4 w-6 h-6 border-t-2 border-r-2 border-white/90"></div>
                    <div class="absolute bottom-4 left-4 w-6 h-6 border-b-2 border-l-2 border-white/90"></div>
                    <div class="absolute bottom-4 right-4 w-6 h-6 border-b-2 border-r-2 border-white/90"></div>
                    <p x-show="cameraError" x-cloak class="absolute inset-0 flex items-center justify-center text-center text-white text-[12.5px] px-8" x-text="cameraError"></p>
                </div>
                <canvas x-ref="canvas" class="hidden"></canvas>

                <div class="flex items-center justify-center py-8">
                    <button type="button" @click="capturePhoto()" :disabled="!!cameraError"
                            class="w-16 h-16 rounded-full border-4 border-white flex items-center justify-center active:scale-95 transition-transform disabled:opacity-40">
                        <span class="w-12 h-12 rounded-full bg-white"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function pengaduanWizard() {
    const categoriesMap = @json($categories->pluck('name', 'id'));

    return {
        steps: ['Identitas', 'Detail kasus', 'Bukti foto', 'Tinjau & kirim'],
        currentStep: {{ $initialStep }},
        furthestStep: {{ max($initialStep, 1) }},
        isAnonymous: {{ old('is_anonymous') ? 'true' : 'false' }},
        isUserLoggedIn: {{ Auth::check() ? 'true' : 'false' }},
        categoriesMap: categoriesMap,
        priority: '{{ old('priority', 'sedang') }}',
        priorityOptions: [
            { value: 'rendah',  label: 'Rendah',  hint: 'Dapat menunggu',         dot: 'bg-neutral-500' },
            { value: 'sedang',  label: 'Sedang',  hint: 'Minggu ini',             dot: 'bg-neutral-300' },
            { value: 'tinggi',  label: 'Tinggi',  hint: 'Ganggu KBM',             dot: 'bg-white' },
            { value: 'darurat', label: 'Darurat', hint: 'Keselamatan / bullying', dot: 'bg-red-500' },
        ],
        form: {
            reporter_name: @json(old('reporter_name', '')),
            reporter_nisn: @json(old('reporter_nisn', '')),
            reporter_class: @json(old('reporter_class', '')),
            reporter_phone: @json(old('reporter_phone', '')),
            reporter_email: @json(old('reporter_email', '')),
            category_id: @json(old('category_id', '')),
            location: @json(old('location', '')),
            title: @json(old('title', '')),
            description: @json(old('description', '')),
        },
        files: [],
        fileErrors: [],
        dragActive: false,
        cameraOpen: false,
        cameraError: '',
        stream: null,
        fieldErrors: {},
        stepErrors: {},

        init() {
            this.$nextTick(() => window.lucide && window.lucide.createIcons());
            this.$watch('currentStep', () => this.$nextTick(() => window.lucide && window.lucide.createIcons()));
            this.$watch('cameraOpen', () => this.$nextTick(() => window.lucide && window.lucide.createIcons()));
            this.$watch('files.length', () => this.$nextTick(() => window.lucide && window.lucide.createIcons()));

            // Populate initial backend field errors if any
            @if ($errors->any())
                @foreach ($errors->messages() as $key => $msgs)
                    this.fieldErrors['{{ $key }}'] = '{{ addslashes($msgs[0]) }}';
                @endforeach
                this.updateStepErrorsSummary();
            @endif
        },

        getCategoryName(id) {
            if (!id) return '';
            return this.categoriesMap[id] || '';
        },

        getStepSubtitle(step) {
            const subtitles = [
                'Data pelapor',
                'Kategori & kronologi',
                'Lampiran berkas',
                'Konfirmasi data'
            ];
            return subtitles[step - 1] || '';
        },

        hasStepError(step) {
            return !!this.stepErrors[step];
        },

        clearFieldError(fieldName) {
            delete this.fieldErrors[fieldName];
            this.updateStepErrorsSummary();
        },

        onAnonymousToggle() {
            if (this.isAnonymous) {
                delete this.fieldErrors.reporter_name;
                delete this.fieldErrors.reporter_phone;
                delete this.fieldErrors.reporter_email;
            }
            this.updateStepErrorsSummary();
        },

        validateStep(stepNumber) {
            let isValid = true;

            if (stepNumber === 1) {
                if (!this.isUserLoggedIn && !this.isAnonymous) {
                    if (!this.form.reporter_name || !this.form.reporter_name.trim()) {
                        this.fieldErrors.reporter_name = 'Nama lengkap pelapor wajib diisi (atau aktifkan mode anonim).';
                        isValid = false;
                    } else if (this.form.reporter_name.trim().length < 3) {
                        this.fieldErrors.reporter_name = 'Nama pelapor minimal 3 karakter.';
                        isValid = false;
                    } else {
                        delete this.fieldErrors.reporter_name;
                    }

                    if (this.form.reporter_email && this.form.reporter_email.trim()) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(this.form.reporter_email.trim())) {
                            this.fieldErrors.reporter_email = 'Format alamat email tidak valid.';
                            isValid = false;
                        } else {
                            delete this.fieldErrors.reporter_email;
                        }
                    }

                    if (this.form.reporter_phone && this.form.reporter_phone.trim()) {
                        const phone = this.form.reporter_phone.trim().replace(/[\s-]/g, '');
                        if (phone.length < 8 || phone.length > 20) {
                            this.fieldErrors.reporter_phone = 'Nomor telepon harus antara 8-20 digit.';
                            isValid = false;
                        } else {
                            delete this.fieldErrors.reporter_phone;
                        }
                    }
                }
            } else if (stepNumber === 2) {
                if (!this.form.category_id) {
                    this.fieldErrors.category_id = 'Pilih kategori pengaduan terlebih dahulu.';
                    isValid = false;
                } else {
                    delete this.fieldErrors.category_id;
                }

                if (!this.form.title || !this.form.title.trim()) {
                    this.fieldErrors.title = 'Judul pengaduan wajib diisi.';
                    isValid = false;
                } else if (this.form.title.trim().length < 4) {
                    this.fieldErrors.title = 'Judul pengaduan terlalu pendek (minimal 4 karakter).';
                    isValid = false;
                } else {
                    delete this.fieldErrors.title;
                }

                if (!this.priority) {
                    this.fieldErrors.priority = 'Tingkat urgensi wajib dipilih.';
                    isValid = false;
                } else {
                    delete this.fieldErrors.priority;
                }

                if (!this.form.description || !this.form.description.trim()) {
                    this.fieldErrors.description = 'Kronologi / rincian kerusakan wajib diisi.';
                    isValid = false;
                } else if (this.form.description.trim().length < 10) {
                    this.fieldErrors.description = 'Kronologi minimal 10 karakter agar laporan jelas dan dapat ditindaklanjuti.';
                    isValid = false;
                } else {
                    delete this.fieldErrors.description;
                }
            } else if (stepNumber === 3) {
                // File uploads are optional, but if file errors exist from previous drop, check them
                if (this.fileErrors.length > 0) {
                    // fileErrors are shown in the step 3 UI
                }
            }

            this.updateStepErrorsSummary();
            return isValid;
        },

        updateStepErrorsSummary() {
            this.stepErrors = {};

            // Step 1 check
            if (this.fieldErrors.reporter_name || this.fieldErrors.reporter_phone || this.fieldErrors.reporter_email) {
                this.stepErrors[1] = this.fieldErrors.reporter_name || this.fieldErrors.reporter_phone || this.fieldErrors.reporter_email;
            }

            // Step 2 check
            if (this.fieldErrors.category_id || this.fieldErrors.title || this.fieldErrors.description || this.fieldErrors.priority) {
                this.stepErrors[2] = this.fieldErrors.category_id || this.fieldErrors.title || this.fieldErrors.description || this.fieldErrors.priority;
            }

            // Step 3 check
            if (this.fieldErrors.attachments || this.fileErrors.length > 0) {
                this.stepErrors[3] = this.fieldErrors.attachments || 'Periksa berkas bukti lampiran Anda.';
            }

            this.$nextTick(() => window.lucide && window.lucide.createIcons());
        },

        goTo(targetStep) {
            // When navigating backwards, always allow
            if (targetStep < this.currentStep) {
                this.currentStep = targetStep;
                return;
            }

            // When navigating forward, validate each prior step sequentially
            for (let s = 1; s < targetStep; s++) {
                if (!this.validateStep(s)) {
                    this.currentStep = s;
                    return;
                }
            }

            this.currentStep = targetStep;
            this.furthestStep = Math.max(this.furthestStep, targetStep);
        },

        next() {
            if (!this.validateStep(this.currentStep)) {
                return;
            }
            this.currentStep = Math.min(this.currentStep + 1, this.steps.length);
            this.furthestStep = Math.max(this.furthestStep, this.currentStep);
        },

        prev() {
            this.currentStep = Math.max(this.currentStep - 1, 1);
        },

        onSubmit(e) {
            // Final validation of steps 1, 2, 3 before submitting
            for (let s = 1; s <= 3; s++) {
                if (!this.validateStep(s)) {
                    e.preventDefault();
                    this.currentStep = s;
                    return;
                }
            }
            this.syncFilesToForm();
        },

        handleFileSelection(fileList) {
            this.fileErrors = [];
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'];
            const maxSize = 5 * 1024 * 1024; // 5MB

            Array.from(fileList).forEach(file => {
                const ext = file.name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(ext)) {
                    this.fileErrors.push(`Berkas "${file.name}" ditolak: format .${ext} tidak didukung (hanya JPG, PNG, WEBP, PDF, DOCX).`);
                    return;
                }

                if (file.size > maxSize) {
                    this.fileErrors.push(`Berkas "${file.name}" ditolak: ukuran melebihi batas 5MB (${(file.size / (1024*1024)).toFixed(2)} MB).`);
                    return;
                }

                const isImage = ['jpg', 'jpeg', 'png', 'webp'].includes(ext);
                const _id = Date.now() + '-' + Math.random().toString(36).slice(2);
                const _preview = isImage ? URL.createObjectURL(file) : null;

                this.files.push({
                    raw: file,
                    name: file.name,
                    size: file.size,
                    extension: ext,
                    isImage: isImage,
                    _id: _id,
                    _preview: _preview
                });
            });

            this.syncFilesToForm();
            this.updateStepErrorsSummary();
        },

        removeFile(index) {
            if (this.files[index]._preview) {
                URL.revokeObjectURL(this.files[index]._preview);
            }
            this.files.splice(index, 1);
            this.syncFilesToForm();
            this.updateStepErrorsSummary();
        },

        formatFileSize(bytes) {
            if (!bytes || bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        },

        syncFilesToForm() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f.raw));
            if (this.$refs.hiddenSubmitInput) {
                this.$refs.hiddenSubmitInput.files = dt.files;
            }
        },

        async openCamera() {
            this.cameraOpen = true;
            this.cameraError = '';
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
                this.$nextTick(() => { this.$refs.video.srcObject = this.stream; });
            } catch (err) {
                this.cameraError = 'Tidak dapat mengakses kamera. Periksa izin akses kamera pada browser Anda.';
            }
        },

        closeCamera() {
            if (this.stream) this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
            this.cameraOpen = false;
        },

        capturePhoto() {
            if (!this.stream) return;
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.toBlob((blob) => {
                const file = new File([blob], `bukti-${Date.now()}.jpg`, { type: 'image/jpeg' });
                this.handleFileSelection([file]);
                this.closeCamera();
            }, 'image/jpeg', 0.92);
        },
    }
}
</script>
@endsection