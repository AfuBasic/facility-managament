<div
    x-data="{
        show: false,
        message: '',
        type: 'success',
        timeout: null,

        open(detail) {
            this.message = detail.message;
            this.type = detail.type ?? 'success';
            this.show = true;

            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => this.close(), 4500);
        },

        close() {
            this.show = false;
        }
    }"
    @toast.window="open($event.detail)"
    class="fixed top-5 right-5 z-50 pointer-events-none"
>
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click="close"
        class="pointer-events-auto cursor-pointer"
    >
        <div
            class="flex items-center gap-3 rounded-xl px-4 py-3 shadow-xl backdrop-blur border
                   bg-white/90 text-gray-900 min-w-[320px]"
            :class="{
                'border-emerald-200': type === 'success',
                'border-red-200': type === 'error',
                'border-amber-200': type === 'warning',
                'border-sky-200': type === 'info',
            }"
        >

            <!-- Icon -->
            <div class="mt-0.5">
                <template x-if="type === 'success'">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>

                <template x-if="type === 'error'">
                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>

                <template x-if="type === 'warning'">
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
            </div>

            <!-- Content -->
            <div class="flex-1 text-sm leading-snug">
                <p x-text="message"></p>
            </div>

            <!-- Close hint -->
            <div class="text-xs text-gray-400 select-none">
                ✕
            </div>
        </div>
    </div>
</div>