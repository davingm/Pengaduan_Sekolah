@props(['status'])

@php
    $config = match ($status) {
        'menunggu_verifikasi' => [
            'label' => 'Menunggu Verifikasi',
            'icon' => 'clock',
            'classes' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/60',
            'dot' => 'bg-amber-500',
        ],
        'ditolak' => [
            'label' => 'Ditolak / Arsip',
            'icon' => 'x-circle',
            'classes' => 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800/60',
            'dot' => 'bg-rose-500',
        ],
        'didisposisikan' => [
            'label' => 'Didisposisikan',
            'icon' => 'user-check',
            'classes' => 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800/60',
            'dot' => 'bg-indigo-500',
        ],
        'diproses' => [
            'label' => 'Sedang Ditangani',
            'icon' => 'loader-2',
            'classes' => 'bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-800/60 animate-pulse',
            'dot' => 'bg-sky-500',
        ],
        'menunggu_persetujuan' => [
            'label' => 'Menunggu Persetujuan Kepsek',
            'icon' => 'file-check',
            'classes' => 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800/60',
            'dot' => 'bg-purple-500',
        ],
        'selesai' => [
            'label' => 'Selesai & Disetujui',
            'icon' => 'check-circle-2',
            'classes' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/60',
            'dot' => 'bg-emerald-500',
        ],
        default => [
            'label' => ucfirst(str_replace('_', ' ', $status)),
            'icon' => 'info',
            'classes' => 'bg-slate-50 text-slate-700 border-slate-200',
            'dot' => 'bg-slate-500',
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border shadow-xs transition ' . $config['classes']]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }}"></span>
    <span>{{ $config['label'] }}</span>
</span>
