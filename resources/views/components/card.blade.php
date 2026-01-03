@props([
    'padding' => 'p-6',
])

<div {{ $attributes->merge([
    'class' => "bg-white/90 backdrop-blur rounded-2xl border border-gray-200 shadow-sm $padding"
]) }}>

    {{-- Header --}}
    @isset($header)
        <div class="mb-4">
            {{ $header }}
        </div>
    @endisset

    {{-- Body --}}
    <div class="text-sm text-gray-700">
        {{ $slot }}
    </div>

    {{-- Footer --}}
    @isset($footer)
        <div class="mt-6 flex items-center gap-2">
            {{ $footer }}
        </div>
    @endisset

</div>