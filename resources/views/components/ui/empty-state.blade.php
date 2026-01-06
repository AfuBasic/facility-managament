@props(['title', 'description'])

<div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300">
    @if(isset($icon))
    <div class="mx-auto h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
        {{ $icon }}
    </div>
    @endif
    <h3 class="text-lg font-medium text-slate-900">{{ $title }}</h3>
    @if(isset($description))
    <p class="text-slate-500 mt-1 text-sm">{{ $description }}</p>
    @endif
    
    @if(isset($actions))
    <div class="mt-6">
        {{ $actions }}
    </div>
    @endif
</div>
