@props(['show', 'title', 'maxWidth' => '4xl'])

@php
    $maxWidths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        '6xl' => 'max-w-6xl',
        '7xl' => 'max-w-7xl',
        'full' => 'max-w-full',
    ];
    $widthClass = $maxWidths[$maxWidth] ?? $maxWidths['4xl'];
@endphp

<div x-data="{ show: @entangle($show) }" 
    x-show="show" 
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm"
    style="display: none;">
    
    <div class="bg-white rounded-2xl shadow-xl w-full {{ $widthClass }} max-h-[90vh] overflow-hidden flex flex-col mx-4" @click.away="show = false">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-900">
                {{ $title }}
            </h3>
            <button @click="show = false" class="text-slate-400 hover:text-slate-500 transition-colors">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Body -->
        <div class="p-6 overflow-y-auto flex-1">
            {{ $slot }}
        </div>
        
        <!-- Footer -->
        @if(isset($footer))
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>
