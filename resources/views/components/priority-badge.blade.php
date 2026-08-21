@props(['priority'])

@php
    $config = match ($priority) {
        'darurat' => [
            'label' => 'Darurat',
            'classes' => 'bg-rose-600 text-white shadow-rose-500/30',
            'icon' => 'flame',
        ],
        'tinggi' => [
            'label' => 'Tinggi',
            'classes' => 'bg-orange-500 text-white shadow-orange-500/30',
            'icon' => 'alert-triangle',
        ],
        'sedang' => [
            'label' => 'Sedang',
            'classes' => 'bg-amber-500 text-white shadow-amber-500/30',
            'icon' => 'gauge',
        ],
        'rendah' => [
            'label' => 'Rendah',
            'classes' => 'bg-slate-500 text-white shadow-slate-500/30',
            'icon' => 'arrow-down',
        ],
        default => [
            'label' => ucfirst($priority),
            'classes' => 'bg-slate-500 text-white',
            'icon' => 'info',
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider shadow-sm ' . $config['classes']]) }}>
    <i data-lucide="{{ $config['icon'] }}" class="w-3 h-3"></i>
    <span>{{ $config['label'] }}</span>
</span>
