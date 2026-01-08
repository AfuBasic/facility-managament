@props(['label', 'description' => null, 'value' => null])

<label {{ $attributes->whereDoesntStartWith('wire:model')->merge(['class' => 'relative flex items-start group cursor-pointer']) }}>
    <div class="flex items-center h-5">
        <input type="checkbox" 
               {{ $attributes->thatStartWith('wire:model') }}
               @if($value) value="{{ $value }}" @endif
               class="peer h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-600 transition duration-150 ease-in-out cursor-pointer">
    </div>
    <div class="ml-3 text-sm leading-5">
        <span class="font-medium text-slate-700 group-hover:text-teal-700 transition-colors">{{ str_replace("_"," ",$label) }}</span>
        @if($description)
            <p class="text-slate-500">{{ $description }}</p>
        @endif
    </div>
</label>
