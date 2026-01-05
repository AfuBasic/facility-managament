@props(['variant' => 'primary', 'type' => 'button'])

@php
    $baseClasses = "inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed";
    
    $variants = [
        'primary' => "border border-transparent text-white bg-teal-600 hover:bg-teal-700 focus:ring-teal-500",
        'secondary' => "bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 focus:ring-teal-500",
        'danger' => "bg-red-600 border border-transparent text-white hover:bg-red-700 focus:ring-red-500",
        'ghost' => "text-slate-400 hover:text-teal-600 hover:bg-teal-50 border border-transparent shadow-none p-1",
        'ghost-danger' => "text-slate-400 hover:text-red-600 hover:bg-red-50 border border-transparent shadow-none p-1"
    ];
    
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
    
    // Ghost buttons usually don't need the standard padding/shadow if they are icon-only, adjusting...
    if (in_array($variant, ['ghost', 'ghost-danger'])) {
        $classes = str_replace(['px-4 py-2', 'shadow-sm'], ['', ''], $classes);
    }
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
