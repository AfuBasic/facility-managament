@props(['title', 'description'])

<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">{{ $title }}</h1>
        @if(isset($description))
        <p class="text-slate-500 mt-2">{{ $description }}</p>
        @endif
    </div>
    <div class="flex justify-end mt-2 md:mt-0 gap-3">
        {{ $actions ?? '' }}
    </div>
</div>
