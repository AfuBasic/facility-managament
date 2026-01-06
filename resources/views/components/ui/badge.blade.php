@props(['variant' => 'neutral'])

@php
    $variants = [
        'neutral' => 'bg-slate-100 text-slate-600 border-slate-200',
        'primary' => 'bg-teal-50 text-teal-700 border-teal-200',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
        'danger' => 'bg-red-50 text-red-700 border-red-200',
        'success' => 'bg-green-50 text-green-700 border-green-200',
    ];
    
    $classes = "inline-flex items-center px-2 py-1 rounded text-xs font-medium border " . ($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
