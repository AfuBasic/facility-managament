@props(['show' => false])

<div 
    x-data="{ 
        show: false,
        title: '',
        message: '',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        confirmAction: null,
        variant: 'danger',
        init() {
            window.addEventListener('confirm-action', (event) => {
                this.title = event.detail.title || 'Confirm Action';
                this.message = event.detail.message || 'Are you sure you want to proceed?';
                this.confirmText = event.detail.confirmText || 'Confirm';
                this.cancelText = event.detail.cancelText || 'Cancel';
                this.variant = event.detail.variant || 'danger';
                this.confirmAction = event.detail.action;
                this.show = true;
            });
        },
        confirm() {
            if (this.confirmAction) {
                this.confirmAction();
            }
            this.show = false;
        },
        cancel() {
            this.show = false;
        }
    }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <!-- Backdrop -->
    <div 
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"
        @click="cancel()"
    ></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div 
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden"
            @click.stop
        >
            <!-- Icon Header -->
            <div class="p-6 pb-4">
                <div class="flex items-start gap-4">
                    <!-- Icon -->
                    <div 
                        class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center"
                        :class="{
                            'bg-red-100': variant === 'danger',
                            'bg-amber-100': variant === 'warning',
                            'bg-teal-100': variant === 'info'
                        }"
                    >
                        <!-- Danger Icon -->
                        <svg x-show="variant === 'danger'" class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <!-- Warning Icon -->
                        <svg x-show="variant === 'warning'" class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <!-- Info Icon -->
                        <svg x-show="variant === 'info'" class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-slate-900 mb-1" x-text="title"></h3>
                        <p class="text-sm text-slate-600" x-text="message"></p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-slate-50 px-6 py-4 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                <button 
                    type="button"
                    @click="cancel()"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors"
                    x-text="cancelText"
                ></button>
                <button 
                    type="button"
                    @click="confirm()"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors shadow-sm"
                    :class="{
                        'bg-red-600 hover:bg-red-700 focus:ring-red-500': variant === 'danger',
                        'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500': variant === 'warning',
                        'bg-teal-600 hover:bg-teal-700 focus:ring-teal-500': variant === 'info'
                    }"
                    x-text="confirmText"
                ></button>
            </div>
        </div>
    </div>
</div>
