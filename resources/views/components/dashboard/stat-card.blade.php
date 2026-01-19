@props(['label', 'value', 'icon' => null, 'trend' => null, 'color' => 'slate'])

<div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between h-full">
    <div class="flex justify-between items-start">
        <div>
            <p class="text-slate-500 text-sm font-medium uppercase tracking-wide">{{ $label }}</p>
            <h3 class="text-3xl font-bold text-slate-900 mt-2">{{ $value }}</h3>
        </div>
        @if($icon)
            <div class="p-3 rounded-xl bg-{{ $color }}-50 text-{{ $color }}-600">
                <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-6 h-6" />
            </div>
        @endif
    </div>
    
    @if($trend)
        <div class="mt-4 flex items-center text-sm">
            <span class="{{ $trend > 0 ? 'text-emerald-600' : 'text-rose-600' }} font-medium flex items-center">
                @if($trend > 0)
                    <x-heroicon-o-arrow-trending-up class="w-4 h-4 mr-1" />
                @else
                    <x-heroicon-o-arrow-trending-down class="w-4 h-4 mr-1" />
                @endif
                {{ abs($trend) }}%
            </span>
            <span class="text-slate-400 ml-2">from last month</span>
        </div>
    @endif
</div>
