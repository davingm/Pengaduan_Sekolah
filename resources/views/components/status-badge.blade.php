@props(['status'])

@php
    $config = match ($status) {
        'menunggu_verifikasi' => [
            'label' => 'Menunggu Verifikasi',
            'icon' => 'clock',
            'classes' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            'dot' => 'bg-amber-400',
        ],
        'ditolak' => [
            'label' => 'Ditolak / Arsip',
            'icon' => 'x-circle',
            'classes' => 'bg-zinc-800/80 text-zinc-400 border-zinc-700/50',
            'dot' => 'bg-zinc-500',
        ],
        'didisposisikan' => [
            'label' => 'Didisposisikan',
            'icon' => 'user-check',
            'classes' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
            'dot' => 'bg-orange-400',
        ],
        'diproses' => [
            'label' => 'Sedang Ditangani',
            'icon' => 'loader-2',
            'classes' => 'bg-orange-500/15 text-orange-300 border-orange-500/30',
            'dot' => 'bg-orange-400 animate-pulse',
        ],
        'menunggu_persetujuan' => [
            'label' => 'Menunggu Persetujuan',
            'icon' => 'file-check',
            'classes' => 'bg-zinc-800 text-zinc-200 border-zinc-700',
            'dot' => 'bg-orange-400',
        ],
        'selesai' => [
            'label' => 'Selesai',
            'icon' => 'check-circle-2',
            'classes' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            'dot' => 'bg-emerald-400',
        ],
        default => [
            'label' => ucfirst(str_replace('_', ' ', $status)),
            'icon' => 'info',
            'classes' => 'bg-zinc-900 text-zinc-300 border-zinc-800',
            'dot' => 'bg-zinc-400',
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-medium border font-mono tracking-tight transition-colors ' . $config['classes']]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }}"></span>
    <span>{{ $config['label'] }}</span>
</span>
