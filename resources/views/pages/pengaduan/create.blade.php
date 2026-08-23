@extends('layouts.app')

@section('title', 'Buat Formulir Pengaduan Sekolah')

@section('content')
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
                Laporkan kerusakan fasilitas, kebutuhan mediasi, atau kendala sekolah lainnya. Empat langkah singkat, sekitar 2 menit.
            </p>
        </div>

        <div class="lg:grid lg:grid-cols-[240px_1fr] lg:gap-16">

            <!-- Desktop: vertical stepper rail -->
            <div class="hidden lg:block">
                <div class="sticky top-14 space-y-1">
                    <template x-for="(step, i) in steps" :key="i">
                        <button type="button" @click="goTo(i + 1)" :disabled="i + 1 > furthestStep"
                                class="w-full flex items-start gap-3 px-3 py-3 text-left border transition-colors disabled:cursor-not-allowed"
                                :class="currentStep === i + 1 ? 'border-neutral-800 bg-neutral-900' : 'border-transparent hover:bg-neutral-900/60'">
                            <span class="w-7 h-7 rounded-full border flex items-center justify-center text-[11px] font-bold font-mono flex-shrink-0"
                                  :class="i + 1 < currentStep ? 'bg-white border-white text-neutral-950' : (currentStep === i + 1 ? 'border-white text-white' : 'border-neutral-700 text-neutral-500')">
                                <i data-lucide="check" class="w-3.5 h-3.5" x-show="i + 1 < currentStep" x-cloak></i>
                                <span x-show="i + 1 >= currentStep" x-text="String(i + 1).padStart(2, '0')"></span>
                            </span>
                            <span class="pt-1.5">
                                <span class="block text-[13px] font-semibold" :class="currentStep === i + 1 ? 'text-white' : 'text-neutral-500'" x-text="step"></span>
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
                            <button type="button" @click="goTo(i + 1)" :disabled="i + 1 > furthestStep"
                                    class="w-8 h-8 rounded-full border flex items-center justify-center text-xs font-bold font-mono flex-shrink-0 transition-colors disabled:cursor-not-allowed"
                                    :class="i + 1 < currentStep ? 'bg-white border-white text-neutral-950' : (currentStep === i + 1 ? 'border-white text-white' : 'border-neutral-700 text-neutral-500')">
                                <i data-lucide="check" class="w-3.5 h-3.5" x-show="i + 1 < currentStep" x-cloak></i>
                                <span x-show="i + 1 >= currentStep" x-text="String(i + 1).padStart(2, '0')"></span>
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
                                <input type="checkbox" name="is_anonymous" value="1" x-model="isAnonymous" class="hidden">
                                <button type="button" @click="isAnonymous = !isAnonymous"
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
                                    <label class="block text-[11.5px] font-semibold text-white mb-1.5">Nama lengkap pelapor *</label>
                                    <input type="text" name="reporter_name" x-model="form.reporter_name" value="{{ old('reporter_name') }}" placeholder="Contoh: Ahmad Fauzan"
                                           class="w-full bg-neutral-950 border border-neutral-800 px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none focus:border-white focus:bg-neutral-900 transition">
                                </div>
                                <div>
                                    <label class="block text-[11.5px] font-semibold text-white mb-1.5">NISN / NIP (opsional)</label>
                                    <input type="text" name="reporter_nisn" value="{{ old('reporter_nisn') }}" placeholder="0068945123"
                                           class="w-full bg-neutral-950 border border-neutral-800 px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none focus:border-white focus:bg-neutral-900 transition">
                                </div>
                                <div>
                                    <label class="block text-[11.5px] font-semibold text-white mb-1.5">Kelas / Rombel</label>
                                    <input type="text" name="reporter_class" value="{{ old('reporter_class') }}" placeholder="XI MIPA 2"
                                           class="w-full bg-neutral-950 border border-neutral-800 px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none focus:border-white focus:bg-neutral-900 transition">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[11.5px] font-semibold text-white mb-1.5">No. WhatsApp / telepon aktif</label>
                                    <input type="text" name="reporter_phone" value="{{ old('reporter_phone') }}" placeholder="08123456789"
                                           class="w-full bg-neutral-950 border border-neutral-800 px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none focus:border-white focus:bg-neutral-900 transition">
                                </div>
                            </div>

                            <div x-show="isAnonymous" x-cloak x-transition class="p-4 text-xs flex items-start gap-2.5 border border-neutral-800 bg-neutral-950">
                                <i data-lucide="shield-check" class="w-4 h-4 flex-shrink-0 mt-0.5 text-white"></i>
                                <div class="text-neutral-400">
                                    <div class="font-bold mb-0.5 text-white">Laporan dikirim secara anonim</div>
                                    Nama dan kontak Anda tidak dicatat maupun ditampilkan ke publik. Status laporan tetap bisa dipantau memakai <strong class="text-white">kode tiket</strong> yang diterbitkan setelah dikirim.
                                </div>
                                <input type="hidden" name="reporter_name" value="Siswa Anonim">
                            </div>
                        @endif

                        <p x-show="errors.step1" x-cloak class="text-[12px] font-medium text-red-400" x-text="errors.step1"></p>
                    </div>

                    <!-- STEP 2 : Detail Pengaduan -->
                    <div x-show="currentStep === 2" x-cloak
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-5">
                        <div>
                            <h2 class="text-[15px] lg:text-base font-bold text-white">Detail Kasus atau Fasilitas</h2>
                            <p class="text-[12px] mt-0.5 text-neutral-400">Semakin spesifik, semakin cepat ditindaklanjuti.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11.5px] font-semibold text-white mb-1.5">Kategori pengaduan *</label>
                                <div class="relative">
                                    <select name="category_id" x-model="form.category_id"
                                            class="appearance-none w-full bg-neutral-950 border border-neutral-800 px-3.5 py-2.5 pr-9 text-sm text-white focus:outline-none focus:border-white transition cursor-pointer">
                                        <option value="">Pilih kategori masalah</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-neutral-500 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11.5px] font-semibold text-white mb-1.5">Lokasi / ruangan spesifik</label>
                                <input type="text" name="location" value="{{ old('location') }}" placeholder="Gedung C lantai 2, Lab Fisika"
                                       class="w-full bg-neutral-950 border border-neutral-800 px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none focus:border-white focus:bg-neutral-900 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11.5px] font-semibold text-white mb-1.5">Judul pengaduan *</label>
                            <input type="text" name="title" x-model="form.title" value="{{ old('title') }}" placeholder="Contoh: Lampu koridor ruang guru mati total"
                                   class="w-full bg-neutral-950 border border-neutral-800 px-3.5 py-2.5 text-sm text-white placeholder-neutral-600 focus:outline-none focus:border-white focus:bg-neutral-900 transition">
                        </div>

                        <div>
                            <label class="block text-[11.5px] font-semibold text-white mb-1.5">Tingkat urgensi *</label>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5">
                                <template x-for="opt in priorityOptions" :key="opt.value">
                                    <label class="border p-3 cursor-pointer transition-colors flex items-start gap-2.5"
                                           :class="priority === opt.value ? 'border-white bg-neutral-800' : 'border-neutral-800 hover:border-neutral-700'">
                                        <input type="radio" name="priority" class="hidden" :value="opt.value" x-model="priority">
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
                            <label class="block text-[11.5px] font-semibold text-white mb-1.5">Kronologi / rincian kerusakan *</label>
                            <textarea name="description" x-model="form.description" rows="5" placeholder="Jelaskan apa yang terjadi, kapan waktunya, dan bagian mana yang bermasalah..."
                                      class="w-full bg-neutral-950 border border-neutral-800 p-3.5 text-sm text-white placeholder-neutral-600 focus:outline-none focus:border-white focus:bg-neutral-900 transition leading-relaxed">{{ old('description') }}</textarea>
                        </div>

                        <p x-show="errors.step2" x-cloak class="text-[12px] font-medium text-red-400" x-text="errors.step2"></p>
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
                             @drop.prevent="dragActive = false; addFiles($event.dataTransfer.files)">
                            <input type="file" x-ref="fileInput" multiple accept="image/*,.pdf,.doc,.docx" @change="addFiles($event.target.files); $event.target.value = ''" class="hidden">
                            <div class="w-10 h-10 mx-auto flex items-center justify-center mb-2 border border-neutral-800 bg-neutral-950">
                                <i data-lucide="upload-cloud" class="w-5 h-5 text-neutral-500"></i>
                            </div>
                            <div class="text-[12px] font-semibold text-white">Seret berkas ke sini, atau klik tombol unggah di atas</div>
                            <p class="text-[10.5px] mt-1 text-neutral-500">JPG, PNG, WEBP, PDF &middot; maksimal 5MB per berkas</p>
                        </div>

                        <!-- Hidden real input synced for submission -->
                        <input type="file" name="attachments[]" x-ref="hiddenSubmitInput" multiple class="hidden">

                        <template x-if="files.length > 0">
                            <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-2.5">
                                <template x-for="(file, index) in files" :key="file._id">
                                    <div class="relative overflow-hidden border border-neutral-800 bg-neutral-950 aspect-square">
                                        <img class="w-full h-full object-cover" :src="file._preview" :alt="file.name">
                                        <button type="button" @click="removeFile(index)"
                                                class="absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-black/70 flex items-center justify-center text-white">
                                            <i data-lucide="x" class="w-3 h-3"></i>
                                        </button>
                                        <div class="absolute bottom-0 inset-x-0 px-1.5 py-1 text-[9px] font-medium text-white truncate bg-gradient-to-t from-black/70 to-transparent" x-text="file.name"></div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- STEP 4 : Review -->
                    <div x-show="currentStep === 4" x-cloak
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-5">
                        <div>
                            <h2 class="text-[15px] lg:text-base font-bold text-white">Tinjau Sebelum Mengirim</h2>
                            <p class="text-[12px] mt-0.5 text-neutral-400">Pastikan detailnya sudah benar. Anda bisa kembali ke langkah sebelumnya untuk mengubah.</p>
                        </div>

                        <div class="border border-neutral-800 divide-y divide-neutral-800">
                            <div class="p-4 flex items-center justify-between">
                                <span class="text-[11.5px] font-semibold text-neutral-500">Pelapor</span>
                                <span class="text-[12.5px] font-semibold text-right text-white" x-text="isAnonymous ? 'Anonim (disamarkan)' : (form.reporter_name || '—')"></span>
                            </div>
                            <div class="p-4 flex items-center justify-between">
                                <span class="text-[11.5px] font-semibold text-neutral-500">Judul</span>
                                <span class="text-[12.5px] font-semibold text-right max-w-[60%] text-white" x-text="form.title || '—'"></span>
                            </div>
                            <div class="p-4 flex items-center justify-between">
                                <span class="text-[11.5px] font-semibold text-neutral-500">Urgensi</span>
                                <span class="text-[12.5px] font-semibold text-right inline-flex items-center gap-1.5 text-white">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="priorityOptions.find(p => p.value === priority)?.dot"></span>
                                    <span x-text="priorityOptions.find(p => p.value === priority)?.label"></span>
                                </span>
                            </div>
                            <div class="p-4 flex items-center justify-between">
                                <span class="text-[11.5px] font-semibold text-neutral-500">Lampiran</span>
                                <span class="text-[12.5px] font-semibold text-white" x-text="files.length + ' berkas'"></span>
                            </div>
                            <div class="p-4">
                                <span class="text-[11.5px] font-semibold block mb-1.5 text-neutral-500">Kronologi</span>
                                <p class="text-[12.5px] leading-relaxed text-white" x-text="form.description || '—'"></p>
                            </div>
                        </div>

                        <div class="p-3.5 text-[11.5px] flex items-start gap-2 bg-neutral-950 border border-neutral-800 text-neutral-400">
                            <i data-lucide="info" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-white"></i>
                            Laporan akan diteruskan ke Guru Piket untuk diperiksa lebih lanjut.
                        </div>
                    </div>

                    <!-- Nav -->
                    <div class="flex items-center justify-between gap-3 mt-8 pt-6 border-t border-neutral-800">
                        <button type="button" @click="prev()" x-show="currentStep > 1" x-cloak
                                class="border border-neutral-700 text-white font-semibold px-5 py-3 text-[12.5px] hover:bg-neutral-800/60 hover:border-neutral-600 transition inline-flex items-center gap-1.5">
                            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali
                        </button>
                        <div x-show="currentStep === 1" class="hidden sm:block"></div>

                        <button type="button" @click="next()" x-show="currentStep < steps.length" x-cloak
                                class="bg-white text-neutral-950 font-semibold px-6 py-3 text-[12.5px] hover:bg-neutral-200 active:scale-[.98] transition inline-flex items-center gap-1.5 ml-auto">
                            Lanjut <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </button>
                        <button type="submit" x-show="currentStep === steps.length" x-cloak
                                class="bg-white text-neutral-950 font-semibold px-6 py-3 text-[12.5px] hover:bg-neutral-200 active:scale-[.98] transition inline-flex items-center gap-1.5 ml-auto">
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
                    <button type="button" @click="closeCamera()" class="w-8 h-8 rounded-full flex items-center justify-center bg-white/10">
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
    return {
        steps: ['Identitas', 'Detail kasus', 'Bukti foto', 'Tinjau & kirim'],
        currentStep: 1,
        furthestStep: 1,
        isAnonymous: {{ old('is_anonymous') ? 'true' : 'false' }},
        priority: '{{ old('priority', 'sedang') }}',
        priorityOptions: [
            { value: 'rendah',  label: 'Rendah',  hint: 'Dapat menunggu',         dot: 'bg-neutral-500' },
            { value: 'sedang',  label: 'Sedang',  hint: 'Minggu ini',             dot: 'bg-neutral-300' },
            { value: 'tinggi',  label: 'Tinggi',  hint: 'Ganggu KBM',             dot: 'bg-white' },
            { value: 'darurat', label: 'Darurat', hint: 'Keselamatan / bullying', dot: 'bg-red-500' },
        ],
        form: {
            reporter_name: '{{ old('reporter_name') }}',
            category_id: '{{ old('category_id') }}',
            title: '{{ old('title') }}',
            description: `{{ old('description') }}`,
        },
        files: [],
        dragActive: false,
        cameraOpen: false,
        cameraError: '',
        stream: null,
        errors: {},

        init() {
            this.$nextTick(() => window.lucide && window.lucide.createIcons());
            this.$watch('currentStep', () => this.$nextTick(() => window.lucide && window.lucide.createIcons()));
            this.$watch('cameraOpen', () => this.$nextTick(() => window.lucide && window.lucide.createIcons()));
        },

        goTo(step) {
            if (step <= this.furthestStep) this.currentStep = step;
        },

        next() {
            if (!this.validateStep(this.currentStep)) return;
            this.currentStep = Math.min(this.currentStep + 1, this.steps.length);
            this.furthestStep = Math.max(this.furthestStep, this.currentStep);
        },

        prev() {
            this.currentStep = Math.max(this.currentStep - 1, 1);
        },

        validateStep(step) {
            this.errors = {};
            if (step === 1 && !this.isAnonymous && {{ Auth::check() ? 'false' : 'true' }}) {
                if (!this.form.reporter_name || !this.form.reporter_name.trim()) {
                    this.errors.step1 = 'Nama lengkap pelapor wajib diisi, atau aktifkan mode anonim.';
                    return false;
                }
            }
            if (step === 2) {
                if (!this.form.category_id) { this.errors.step2 = 'Pilih kategori pengaduan terlebih dahulu.'; return false; }
                if (!this.form.title || !this.form.title.trim()) { this.errors.step2 = 'Judul pengaduan wajib diisi.'; return false; }
                if (!this.form.description || !this.form.description.trim()) { this.errors.step2 = 'Kronologi wajib diisi.'; return false; }
            }
            return true;
        },

        onSubmit(e) {
            if (!this.validateStep(1) || !this.validateStep(2)) {
                e.preventDefault();
                this.currentStep = this.errors.step1 ? 1 : 2;
                return;
            }
            this.syncFilesToForm();
        },

        addFiles(fileList) {
            Array.from(fileList).forEach(file => {
                if (file.size > 5 * 1024 * 1024) return;
                const _id = Date.now() + '-' + Math.random().toString(36).slice(2);
                const _preview = URL.createObjectURL(file);
                this.files.push({ raw: file, name: file.name, _id, _preview });
            });
            this.syncFilesToForm();
        },

        removeFile(index) {
            URL.revokeObjectURL(this.files[index]._preview);
            this.files.splice(index, 1);
            this.syncFilesToForm();
        },

        syncFilesToForm() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f.raw));
            this.$refs.hiddenSubmitInput.files = dt.files;
        },

        async openCamera() {
            this.cameraOpen = true;
            this.cameraError = '';
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
                this.$nextTick(() => { this.$refs.video.srcObject = this.stream; });
            } catch (err) {
                this.cameraError = 'Tidak dapat mengakses kamera. Periksa izin kamera pada browser Anda.';
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
                this.addFiles([file]);
                this.closeCamera();
            }, 'image/jpeg', 0.92);
        },
    }
}
</script>
@endsection