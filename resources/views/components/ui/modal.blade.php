@props(['show' => null, 'title', 'maxWidth' => '4xl'])

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

<div
    x-data="{ show: @if($show) @entangle($show) @else false @endif }"
    x-modelable="show"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    style="display: none;"
    {{ $attributes }}
>
    <template x-teleport="body">
        <div
            x-show="show"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity"
                    @click="show = false"
                    aria-hidden="true"
                ></div>

                {{-- Spacer for centering on desktop --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal Panel --}}
                <div
                    x-show="show"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block transform overflow-hidden rounded-2xl bg-white text-left align-bottom shadow-2xl transition-all sm:my-8 sm:w-full {{ $widthClass }} sm:align-middle border border-slate-200"
                    @click.stop
                >
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
                        <h3 class="text-lg font-semibold text-slate-900" id="modal-title">
                            {{ $title }}
                        </h3>
                        <button @click="show = false" class="text-slate-400 hover:text-slate-500 transition-colors p-1 rounded-full hover:bg-slate-100">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-6">
                        {{ $slot }}
                    </div>

                    <!-- Footer -->
                    @if(isset($footer))
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-2xl">
                        {{ $footer }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </template>
</div>
