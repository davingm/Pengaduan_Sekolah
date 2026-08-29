@props(['priority'])

@php
    $config = match ($priority) {
        'darurat' => [
            'label' => 'Darurat',
            'classes' => 'bg-red-500/10 text-red-400 border-red-500/20',
            'icon' => 'alert-circle',
        ],
        'tinggi' => [
            'label' => 'Tinggi',
            'classes' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
            'icon' => 'alert-triangle',
        ],
        'sedang' => [
            'label' => 'Sedang',
            'classes' => 'bg-zinc-800/90 text-zinc-300 border-zinc-700/60',
            'icon' => 'minus',
        ],
        'rendah' => [
            'label' => 'Rendah',
            'classes' => 'bg-zinc-900 text-zinc-400 border-zinc-800',
            'icon' => 'arrow-down',
        ],
        default => [
            'label' => ucfirst($priority),
            'classes' => 'bg-zinc-900 text-zinc-400 border-zinc-800',
            'icon' => 'info',
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-mono font-medium tracking-tight border ' . $config['classes']]) }}>
    <i data-lucide="{{ $config['icon'] }}" class="w-3 h-3"></i>
    <span>{{ $config['label'] }}</span>
</span>
