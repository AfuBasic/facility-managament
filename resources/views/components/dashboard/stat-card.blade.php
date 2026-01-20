@props(['label', 'value', 'icon' => null, 'trend' => null, 'color' => 'slate', 'link' => '#'])

@php
    $colorClasses = [
        'slate' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'hover' => 'hover:border-slate-200'],
        'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'hover' => 'hover:border-blue-200'],
        'teal' => ['bg' => 'bg-teal-50', 'text' => 'text-teal-600', 'hover' => 'hover:border-teal-200'],
        'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'hover' => 'hover:border-amber-200'],
        'indigo' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'hover' => 'hover:border-indigo-200'],
        'emerald' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'hover' => 'hover:border-emerald-200'],
        'rose' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'hover' => 'hover:border-rose-200'],
    ];
    $colors = $colorClasses[$color] ?? $colorClasses['slate'];
@endphp

<a href="{{ $link }}" {{ $link !== '#' ? 'wire:navigate' : '' }} class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center gap-4 hover:shadow-md {{ $colors['hover'] }} transition-all duration-200 group">
    @if($icon)
        <div class="p-3 rounded-xl {{ $colors['bg'] }} {{ $colors['text'] }} group-hover:scale-105 transition-transform">
            <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-5 h-5" />
        </div>
    @endif
    <div class="flex-1 min-w-0">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide truncate">{{ $label }}</p>
        <div class="flex items-baseline gap-2">
            <h3 class="text-2xl font-bold text-slate-900">{{ $value }}</h3>
            @if($trend)
                <span class="text-xs font-medium flex items-center {{ $trend > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    @if($trend > 0)
                        <x-heroicon-o-arrow-trending-up class="w-3 h-3 mr-0.5" />
                    @else
                        <x-heroicon-o-arrow-trending-down class="w-3 h-3 mr-0.5" />
                    @endif
                    {{ abs($trend) }}%
                </span>
            @endif
        </div>
    </div>
</a>
