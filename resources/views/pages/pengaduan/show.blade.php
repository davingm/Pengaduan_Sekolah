@extends('layouts.app')

@section('title', 'Status Pengaduan: ' . $complaint->ticket_code)

@section('content')
@php
    $activeStep = 1;
    $isRejected = $complaint->status === 'ditolak';
    $isProcessing = $complaint->status === 'diproses';
    $isWaitingApproval = $complaint->status === 'menunggu_persetujuan';

    if (!$isRejected) {
        if (in_array($complaint->status, ['didisposisikan']))       $activeStep = 2;
        if (in_array($complaint->status, ['diproses']))             $activeStep = 3;
        if (in_array($complaint->status, ['menunggu_persetujuan'])) $activeStep = 4;
        if ($complaint->status === 'selesai')                       $activeStep = 5;
    } else {
        $activeStep = 2;
    }

    $steps = [
        ['num' => 1, 'label' => 'Terkirim',    'sub' => 'Pelapor'],
        ['num' => 2, 'label' => 'Verifikasi',   'sub' => 'Piket'],
        ['num' => 3, 'label' => 'Penanganan',   'sub' => 'Petugas'],
        ['num' => 4, 'label' => 'Selesai',      'sub' => 'Kepsek'],
    ];
@endphp

<div x-data="{ lightboxSrc: null }" class="min-h-screen bg-[#09090b]">

    <!-- ═══════ Lightbox ═══════ -->
    <div x-show="lightboxSrc"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="lightboxSrc = null"
         @keydown.escape.window="lightboxSrc = null"
         class="fixed inset-0 z-[100] bg-black/95 backdrop-blur-md flex items-center justify-center p-4 sm:p-8 cursor-pointer"
         x-cloak>
        <img :src="lightboxSrc"
             class="max-w-full max-h-[88vh] object-contain rounded-lg select-none cursor-default"
             @click.stop>
        <button @click="lightboxSrc = null"
                class="absolute top-4 right-4 sm:top-6 sm:right-6 w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/20 text-white/60 hover:text-white transition-colors">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- ═══════ Main Container ═══════ -->
    <div class="max-w-3xl mx-auto px-5 sm:px-8 lg:px-10 py-8 lg:py-16">

        <!-- ─── Nav Bar ─── -->
        <nav class="flex items-center justify-between pb-5 border-b border-white/[0.06]">
            <a href="{{ route('home') }}"
               class="group inline-flex items-center gap-2 text-[14px] text-neutral-500 hover:text-white transition-colors duration-200">
                <i data-lucide="arrow-left" class="w-[18px] h-[18px] transition-transform duration-200 group-hover:-translate-x-0.5"></i>
                Kembali
            </a>
            <div class="flex items-center gap-3">
                @if(Auth::check() && (Auth::user()->isStaff() || Auth::id() === $complaint->user_id))
                    <a href="{{ route('dashboard.pengaduan.show', $complaint->ticket_code) }}"
                       class="hidden sm:inline-flex items-center gap-1.5 text-[13px] text-neutral-600 hover:text-white transition-colors duration-200">
                        Dashboard
                        <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
                <code class="text-[12px] sm:text-[13px] font-mono text-neutral-600 bg-white/[0.04] px-2.5 py-1 rounded-lg border border-white/[0.06] select-all">{{ $complaint->ticket_code }}</code>
            </div>
        </nav>

        <!-- ─── Title Hero ─── -->
        <header class="pt-10 pb-2">
            <div class="flex flex-wrap items-center gap-2 mb-5">
                <x-status-badge :status="$complaint->status" />
                <x-priority-badge :priority="$complaint->priority" />
            </div>
            <h1 class="text-[26px] sm:text-[32px] lg:text-[38px] font-bold text-white tracking-[-0.02em] leading-[1.15]">
                {{ $complaint->title }}
            </h1>
            <p class="mt-3.5 text-[15px] text-neutral-500 leading-relaxed">
                {{ $complaint->created_at->format('d F Y · H.i') }} WIB
                @if(!$complaint->is_anonymous)
                    — {{ $complaint->reporter_name }}{{ $complaint->reporter_class ? ' · ' . $complaint->reporter_class : '' }}
                @else
                    — Dilaporkan anonim
                @endif
            </p>
        </header>

        <!-- ─── Stepper ─── -->
        <section class="py-10 border-y border-white/[0.06]">
            <div class="flex items-start">
                @foreach($steps as $i => $step)
                    @php
                        $num = $step['num'];
                        if ($isRejected && $num === 2) {
                            $state = 'rejected';
                        } elseif ($activeStep > $num) {
                            $state = 'completed';
                        } elseif ($activeStep === $num) {
                            $state = 'active';
                        } else {
                            $state = 'pending';
                        }
                    @endphp

                    <!-- Step Node -->
                    <div class="flex flex-col items-center shrink-0">
                        <div class="relative">
                            @if($state === 'completed')
                                <div class="w-10 h-10 rounded-full bg-white text-[#09090b] flex items-center justify-center shadow-sm shadow-white/10">
                                    <i data-lucide="check" class="w-[18px] h-[18px]" stroke-width="2.5"></i>
                                </div>
                            @elseif($state === 'active')
                                <div class="w-10 h-10 rounded-full border-2 border-white bg-[#09090b] flex items-center justify-center step-pulse">
                                    @if($isProcessing && $num === 3)
                                        <i data-lucide="loader" class="w-[18px] h-[18px] text-white animate-spin"></i>
                                    @else
                                        <div class="w-3 h-3 rounded-full bg-white"></div>
                                    @endif
                                </div>
                            @elseif($state === 'rejected')
                                <div class="w-10 h-10 rounded-full bg-white text-[#09090b] flex items-center justify-center">
                                    <i data-lucide="x" class="w-[18px] h-[18px]" stroke-width="2.5"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-full border-[1.5px] border-white/10 bg-[#09090b] flex items-center justify-center">
                                    <span class="text-[13px] font-semibold text-neutral-700">{{ $num }}</span>
                                </div>
                            @endif
                        </div>
                        <span class="mt-3 text-[13px] font-medium {{ $state === 'pending' ? 'text-neutral-700' : 'text-white' }} whitespace-nowrap">
                            {{ $step['label'] }}
                        </span>
                        <span class="mt-0.5 text-[11px] text-neutral-700 hidden sm:block">{{ $step['sub'] }}</span>
                    </div>

                    <!-- Connecting Line -->
                    @if($i < 3)
                        @php
                            $nextNum = $steps[$i + 1]['num'];
                            if ($isRejected && $nextNum === 2) {
                                $lineActive = false;
                            } elseif ($activeStep > $nextNum) {
                                $lineActive = true;
                            } elseif ($activeStep === $nextNum) {
                                $lineActive = true;
                            } else {
                                $lineActive = false;
                            }
                        @endphp
                        <div class="flex-1 flex items-center" style="height:40px;">
                            <div class="w-full h-px transition-colors duration-500 {{ $lineActive ? 'bg-white' : 'bg-white/[0.06]' }}"></div>
                        </div>
                    @endif
                @endforeach
            </div>
        </section>

        <!-- ─── Rejection Alert ─── -->
        @if($isRejected)
            <div class="mt-8 p-5 sm:p-6 rounded-2xl bg-white/[0.04] border border-white/[0.08] space-y-2.5">
                <div class="flex items-center gap-2.5 text-[15px] font-semibold text-white">
                    <i data-lucide="octagon-x" class="w-[18px] h-[18px] text-neutral-400"></i>
                    Pengaduan Ditolak & Diarsipkan
                </div>
                <p class="text-[14px] text-neutral-500 leading-[1.7]">{{ $complaint->rejection_reason }}</p>
            </div>
        @endif

        <!-- ─── Detail Info ─── -->
        <section class="mt-10">
            <h2 class="text-[12px] font-semibold text-neutral-600 uppercase tracking-[0.08em] mb-5">Detail Pengaduan</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-10">
                @php
                    $details = [
                        ['Kategori' => $complaint->category->name],
                        ['Lokasi' => $complaint->location ?? '—'],
                        ['Pelapor' => $complaint->is_anonymous ? 'Anonim' : $complaint->reporter_name . ($complaint->reporter_class ? ' ('.$complaint->reporter_class.')' : '')],
                        ['Verifikator' => $complaint->assignedByGuru->name ?? 'Menunggu'],
                        ['Petugas' => $complaint->assignedOfficer->name ?? 'Belum ditugaskan'],
                        ['Diperbarui' => $complaint->updated_at->diffForHumans()],
                    ];
                @endphp
                @foreach($details as $detail)
                    @foreach($detail as $key => $val)
                        <div class="flex justify-between items-baseline py-3.5 border-b border-white/[0.04] text-[14px]">
                            <span class="text-neutral-600 shrink-0 mr-4">{{ $key }}</span>
                            <span class="text-neutral-200 font-medium text-right">{{ $val }}</span>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </section>

        <!-- ─── Description ─── -->
        <section class="mt-10">
            <h2 class="text-[12px] font-semibold text-neutral-600 uppercase tracking-[0.08em] mb-4">Uraian Laporan</h2>
            <div class="text-[15px] text-neutral-400 leading-[1.8] whitespace-pre-line pl-px">{{ $complaint->description }}</div>
        </section>

        <!-- ─── Evidence Photos + Camera ─── -->
        @if($complaint->evidenceAttachments->isNotEmpty() || true)
            <section class="mt-12">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-[12px] font-semibold text-neutral-600 uppercase tracking-[0.08em] flex items-center gap-2">
                        <i data-lucide="image" class="w-3.5 h-3.5"></i>
                        Bukti Foto
                    </h2>
                    @if($complaint->evidenceAttachments->isNotEmpty())
                        <span class="text-[12px] text-neutral-700 font-mono">{{ $complaint->evidenceAttachments->count() }}</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($complaint->evidenceAttachments as $att)
                        @if(Str::startsWith($att->file_type, 'image/'))
                            <button @click="lightboxSrc = '{{ asset('storage/' . $att->file_path) }}'"
                                    class="group relative aspect-[4/3] rounded-2xl overflow-hidden bg-white/[0.04] border border-white/[0.06] cursor-zoom-in focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30 focus-visible:ring-offset-2 focus-visible:ring-offset-[#09090b]">
                                <img src="{{ asset('storage/' . $att->file_path) }}"
                                     alt="{{ $att->file_name }}"
                                     class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.04]"
                                     loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between">
                                        <i data-lucide="maximize-2" class="w-4 h-4 text-white/70"></i>
                                        <i data-lucide="camera" class="w-4 h-4 text-white/40"></i>
                                    </div>
                                </div>
                            </button>
                        @else
                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank"
                               class="group flex flex-col items-center justify-center aspect-[4/3] rounded-2xl bg-white/[0.02] border border-white/[0.06] hover:border-white/[0.12] transition-colors duration-200">
                                <i data-lucide="file-text" class="w-7 h-7 text-neutral-700 group-hover:text-neutral-500 transition-colors mb-2"></i>
                                <span class="text-[11px] text-neutral-600 font-medium px-3 text-center truncate max-w-full">{{ $att->file_name }}</span>
                            </a>
                        @endif
                    @endforeach

                    <!-- Camera Capture Button -->
                    <label class="group flex flex-col items-center justify-center aspect-[4/3] rounded-2xl border-2 border-dashed border-white/[0.08] hover:border-white/[0.2] bg-white/[0.02] hover:bg-white/[0.04] cursor-pointer transition-all duration-200">
                        <input type="file" accept="image/*" capture="environment" multiple class="hidden"
                               onchange="if(this.files.length){ const fd=new FormData(); Array.from(this.files).forEach((f,i)=>fd.append('evidence[]',f)); fetch(window.location.href,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:fd}).then(r=>r.ok?window.location.reload():null); }">
                        <div class="w-10 h-10 rounded-xl bg-white/[0.04] border border-white/[0.08] group-hover:border-white/[0.15] group-hover:bg-white/[0.06] flex items-center justify-center transition-all duration-200 group-hover:-translate-y-0.5">
                            <i data-lucide="camera" class="w-5 h-5 text-neutral-600 group-hover:text-neutral-300 transition-colors"></i>
                        </div>
                        <span class="mt-2.5 text-[11px] font-medium text-neutral-700 group-hover:text-neutral-400 transition-colors">Ambil Foto</span>
                    </label>
                </div>
            </section>
        @endif

        <!-- ─── Resolution ─── -->
        @if($complaint->resolution_notes || $complaint->resolutionAttachments->isNotEmpty() || $complaint->approval_notes)
            <section class="mt-12 pl-5 border-l-2 border-white/80 space-y-5">
                <div class="flex items-center gap-2.5">
                    <i data-lucide="check-check" class="w-[18px] h-[18px] text-white"></i>
                    <h2 class="text-[15px] font-semibold text-white">Tindak Lanjut & Penyelesaian</h2>
                </div>

                @if($complaint->resolution_notes)
                    <div class="text-[14px] text-neutral-400 leading-[1.75] whitespace-pre-line pl-7">
                        <span class="block text-[12px] font-semibold text-neutral-600 uppercase tracking-[0.06em] mb-2">Catatan Petugas</span>
                        {{ $complaint->resolution_notes }}
                    </div>
                @endif

                @if($complaint->approval_notes)
                    <div class="text-[14px] text-neutral-400 leading-[1.75] whitespace-pre-line pl-7">
                        <span class="block text-[12px] font-semibold text-neutral-600 uppercase tracking-[0.06em] mb-2">Catatan Kepala Sekolah</span>
                        {{ $complaint->approval_notes }}
                    </div>
                @endif

                @if($complaint->resolutionAttachments->isNotEmpty())
                    <div class="pl-7 space-y-3">
                        <span class="block text-[12px] font-semibold text-neutral-600 uppercase tracking-[0.06em]">Bukti Setelah Penanganan</span>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($complaint->resolutionAttachments as $att)
                                @if(Str::startsWith($att->file_type, 'image/'))
                                    <button @click="lightboxSrc = '{{ asset('storage/' . $att->file_path) }}'"
                                            class="group relative aspect-[4/3] rounded-2xl overflow-hidden bg-white/[0.04] border border-white/[0.06] cursor-zoom-in focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30 focus-visible:ring-offset-2 focus-visible:ring-offset-[#09090b]">
                                        <img src="{{ asset('storage/' . $att->file_path) }}"
                                             alt="{{ $att->file_name }}"
                                             class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-[1.04]"
                                             loading="lazy">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <div class="absolute bottom-3 left-3">
                                                <i data-lucide="maximize-2" class="w-4 h-4 text-white/70"></i>
                                            </div>
                                        </div>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @endif

        <!-- ─── Satisfaction Rating ─── -->
        @if($complaint->status === 'selesai')
            <section class="mt-12 p-6 sm:p-8 rounded-2xl bg-white/[0.03] border border-white/[0.06] space-y-5"
                     x-data="{ rating: {{ $complaint->satisfaction_rating ?? 0 }}, hover: 0, submitted: {{ $complaint->satisfaction_rating ? 'true' : 'false' }} }">

                <div class="flex items-center justify-between">
                    <h2 class="text-[15px] font-semibold text-white flex items-center gap-2">
                        <i data-lucide="star" class="w-[18px] h-[18px] text-neutral-400"></i>
                        Penilaian Kepuasan
                    </h2>
                    @if($complaint->satisfaction_rating)
                        <span class="text-[12px] font-mono font-semibold text-neutral-500 bg-white/[0.04] px-3 py-1 rounded-lg border border-white/[0.06]">
                            {{ $complaint->satisfaction_rating }}/5
                        </span>
                    @endif
                </div>

                @if($complaint->satisfaction_rating)
                    <div class="pl-px">
                        <p class="text-[14px] text-neutral-500 italic leading-[1.7]">
                            "{{ $complaint->satisfaction_feedback ?? 'Tidak ada ulasan tertulis.' }}"
                        </p>
                        <p class="mt-2 text-[12px] text-neutral-700 font-mono">
                            {{ $complaint->feedback_submitted_at?->format('d M Y, H.i') }} WIB
                        </p>
                    </div>
                @else
                    <form action="{{ route('pengaduan.rate', $complaint->ticket_code) }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-[14px] text-neutral-400 mb-3">Seberapa puas Anda dengan penanganan ini?</label>
                            <div class="flex items-center gap-1.5">
                                <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                    <button type="button"
                                            @click="rating = star"
                                            @mouseenter="hover = star"
                                            @mouseleave="hover = 0"
                                            class="w-11 h-11 rounded-xl flex items-center justify-center transition-all duration-150 border"
                                            :class="(hover || rating) >= star
                                                ? 'bg-white text-[#09090b] border-white shadow-sm shadow-white/10'
                                                : 'bg-transparent text-neutral-700 border-white/[0.08] hover:border-white/20'">
                                        <i data-lucide="star" class="w-5 h-5" :class="(hover || rating) >= star ? 'fill-current' : ''"></i>
                                    </button>
                                </template>
                                <input type="hidden" name="satisfaction_rating" :value="rating">
                                <span class="ml-3 text-[13px] font-mono font-semibold text-neutral-500"
                                      x-show="rating > 0"
                                      x-text="rating + '/5'"
                                      x-transition></span>
                            </div>
                        </div>
                        <div>
                            <textarea name="satisfaction_feedback" rows="2"
                                      placeholder="Ulasan singkat (opsional)..."
                                      class="w-full bg-white/[0.03] border border-white/[0.08] rounded-xl px-4 py-3 text-[14px] text-white placeholder:text-neutral-700 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent transition-shadow resize-none"></textarea>
                        </div>
                        <button type="submit"
                                :disabled="rating === 0"
                                class="inline-flex items-center gap-2 bg-white hover:bg-neutral-200 disabled:bg-white/[0.06] disabled:text-neutral-700 text-[#09090b] font-semibold px-6 py-3 rounded-xl text-[14px] transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30 focus-visible:ring-offset-2 focus-visible:ring-offset-[#09090b]">
                            Kirim Penilaian
                        </button>
                    </form>
                @endif
            </section>
        @endif

        <!-- ─── Divider ─── -->
        <div class="mt-14 border-t border-white/[0.06]"></div>

        <!-- ─── Timeline & Messages ─── -->
        <section class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14">

            <!-- Timeline -->
            <div>
                <h2 class="text-[12px] font-semibold text-neutral-600 uppercase tracking-[0.08em] mb-6 flex items-center gap-2">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                    Riwayat Aktivitas
                </h2>
                <div class="relative pl-6">
                    <div class="absolute left-[7px] top-2 bottom-2 w-px bg-white/[0.06]"></div>

                    <div class="space-y-5">
                        @foreach($complaint->logs as $log)
                            <div class="relative">
                                <div class="absolute -left-6 top-1.5 w-[15px] h-[15px] rounded-full border-2 border-white/60 bg-[#09090b] z-10"></div>
                                <div>
                                    <div class="flex items-baseline justify-between gap-3 mb-1">
                                        <span class="text-[14px] font-semibold text-white">{{ $log->actor_name }}</span>
                                        <span class="text-[11px] font-mono text-neutral-700 whitespace-nowrap shrink-0">{{ $log->created_at->format('d M, H.i') }}</span>
                                    </div>
                                    <p class="text-[13px] text-neutral-500 leading-[1.65]">{{ $log->notes }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="flex flex-col">
                <h2 class="text-[12px] font-semibold text-neutral-600 uppercase tracking-[0.08em] mb-6 flex items-center gap-2">
                    <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                    Pesan & Klarifikasi
                </h2>

                <div class="flex-1 space-y-3 max-h-[420px] overflow-y-auto pr-1 scrollbar-dark">
                    @forelse($complaint->responses->where('is_internal', false) as $res)
                        <div class="p-4 rounded-2xl text-[13px] leading-[1.65] {{
                            $res->user_id === $complaint->user_id
                                ? 'bg-white text-[#09090b]'
                                : 'bg-white/[0.04] text-neutral-300 border border-white/[0.06]'
                        }}">
                            <div class="flex items-baseline justify-between gap-2 mb-1.5">
                                <span class="font-semibold {{ $res->user_id === $complaint->user_id ? 'text-[#09090b]' : 'text-white' }}">
                                    {{ $res->sender_name }}
                                </span>
                                <span class="text-[11px] {{ $res->user_id === $complaint->user_id ? 'text-neutral-500' : 'text-neutral-700' }} font-mono whitespace-nowrap">
                                    {{ $res->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="{{ $res->user_id === $complaint->user_id ? 'text-neutral-600' : 'text-neutral-500' }}">{{ $res->message }}</p>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-white/[0.03] border border-white/[0.06] flex items-center justify-center mb-3">
                                <i data-lucide="message-square-dashed" class="w-5 h-5 text-neutral-800"></i>
                            </div>
                            <p class="text-[13px] text-neutral-700">Belum ada pesan</p>
                        </div>
                    @endforelse
                </div>

                <form action="{{ route('pengaduan.response', $complaint->ticket_code) }}" method="POST" class="mt-4 space-y-3 pt-5 border-t border-white/[0.06]">
                    @csrf
                    @if(!Auth::check())
                        <input type="text" name="sender_name" required placeholder="Nama Anda"
                               class="w-full bg-white/[0.03] border border-white/[0.08] rounded-xl px-4 py-3 text-[14px] text-white placeholder:text-neutral-700 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent transition-shadow">
                    @endif
                    <div class="flex gap-2">
                        <textarea name="message" rows="1" required placeholder="Tulis pesan..."
                                  class="flex-1 bg-white/[0.03] border border-white/[0.08] rounded-xl px-4 py-3 text-[14px] text-white placeholder:text-neutral-700 focus:outline-none focus:ring-2 focus:ring-white/20 focus:border-transparent transition-shadow resize-none"></textarea>
                        <button type="submit"
                                class="shrink-0 w-11 h-11 flex items-center justify-center rounded-xl bg-white hover:bg-neutral-200 text-[#09090b] transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/30 focus-visible:ring-offset-2 focus-visible:ring-offset-[#09090b]">
                            <i data-lucide="arrow-up" class="w-4 h-4"></i>
                        </button>
                    </div>
                </form>
            </div>

        </section>

        <!-- ─── Bottom Spacing ─── -->
        <div class="h-16"></div>

    </div>
</div>

<style>
    [x-cloak] { display: none !important; }

    @keyframes step-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.12); }
        50%      { box-shadow: 0 0 0 8px rgba(255, 255, 255, 0); }
    }
    .step-pulse {
        animation: step-pulse 2.4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .scrollbar-dark::-webkit-scrollbar { width: 4px; }
    .scrollbar-dark::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-dark::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.06); border-radius: 999px; }
    .scrollbar-dark::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.12); }
</style>
@endsection