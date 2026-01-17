<div
    x-cloak
    x-data="{
        show: false,
        message: '',
        type: 'success',
        position: 'top',
        timeout: null,

        open(detail) {
            this.message = detail.message;
            this.type = detail.type ?? 'success';
            this.position = detail.position ?? 'top';
            this.show = true;

            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => this.close(), 4500);
        },

        close() {
            this.show = false;
        }
    }"
    @toast.window="open($event.detail)"
    class="fixed right-2 md:right-5 z-[100] pointer-events-none"
    :class="position === 'bottom' ? 'bottom-5' : 'top-5'"
>
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click="close"
        class="pointer-events-auto cursor-pointer"
    >
        <div
            class="flex items-start gap-3 rounded-xl px-4 py-3 shadow-xl backdrop-blur
                   min-w-[320px] border"
            :class="{
                /* SUCCESS */
                'bg-emerald-50/90 border-emerald-300 text-emerald-900': type === 'success',

                /* ERROR (very clear) */
                'bg-red-50/95 border-red-400 text-red-900': type === 'error',

                /* WARNING */
                'bg-amber-50/95 border-amber-400 text-amber-900': type === 'warning',

                /* INFO */
                'bg-sky-50/95 border-sky-400 text-sky-900': type === 'info',
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

                <template x-if="type === 'info'">
                    <svg class="h-5 w-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </template>
            </div>

            <!-- Message -->
            <div class="flex-1 text-sm leading-snug">
                <p
                    x-text="message"
                    :class="{ 'font-medium': type === 'error' }"
                ></p>
            </div>

            <!-- Close -->
            <div
                class="text-xs select-none"
                :class="{
                    'text-red-400': type === 'error',
                    'text-emerald-400': type === 'success',
                    'text-amber-400': type === 'warning',
                    'text-sky-400': type === 'info',
                }"
            >
                ✕
            </div>
        </div>
    </div>
</div>
