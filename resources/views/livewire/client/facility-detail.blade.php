<div class="p-2 space-y-2 md:p-6 md:space-y-6">
    {{-- Facility Header --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="flex flex-col gap-5 md:flex-row items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('app.facilities') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-slate-900">{{ $facility->name }}</h1>
                </div>
                
                @if($facility->address)
                    <p class="text-sm text-slate-600 flex items-center gap-2 mt-2">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        {{ $facility->address }}
                    </p>
                @endif
            </div>

            {{-- Contact Info --}}
            @if($facility->contact_person_name || $facility->contact_person_phone)
                <div class="md:ml-6 bg-slate-50 rounded-lg p-4 min-w-full md:min-w-[250px]">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">Contact Person</p>
                    @if($facility->contact_person_name)
                        <p class="text-sm text-slate-700 flex items-center gap-2 mb-2">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            {{ $facility->contact_person_name }}
                        </p>
                    @endif
                    @if($facility->contact_person_phone)
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-slate-700 flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                                {{ $facility->contact_person_phone }}
                            </p>
                            <button 
                                onclick="copyToClipboard('{{ $facility->contact_person_phone }}')" 
                                class="text-slate-400 hover:text-teal-600 transition-colors"
                                title="Copy phone number"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-xl border border-slate-200">
        {{-- Tab Headers --}}
        @include('livewire.client.facility-detail.partials.tab-navigation')

        {{-- Tab Content --}}
        <div class="p-6">
            @if($activeTab === 'spaces' && auth()->user()->can('view spaces'))
                <livewire:client.facility-detail.facility-spaces :facility="$facility" :clientAccount="$clientAccount" :key="'spaces-'.$facility->id" />
            @elseif($activeTab === 'stores' && auth()->user()->can('view stores'))
                <livewire:client.facility-detail.facility-stores :facility="$facility" :clientAccount="$clientAccount" :key="'stores-'.$facility->id" />
            @elseif($activeTab === 'managers' && auth()->user()->can('view facility_managers'))
                <livewire:client.facility-detail.facility-managers :facility="$facility" :clientAccount="$clientAccount" :key="'managers-'.$facility->id" />
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                        <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-700 mb-2">No Access</h3>
                    <p class="text-sm text-slate-500">You don't have permission to view this section</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Tab URL Update Script --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('update-url', (event) => {
                const url = new URL(window.location);
                url.searchParams.set('tab', event.tab);
                window.history.pushState({}, '', url);
            });
        });
    </script>
</div>

<script>
    function copyToClipboard(text) {
        // Check if clipboard API is available
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                // Dispatch Livewire event to show success notification
                window.Livewire.dispatch('toast', {
                    type: 'success',
                    message: 'Phone number copied to clipboard!'
                });
            }).catch(err => {
                console.error('Failed to copy:', err);
                window.Livewire.dispatch('toast', {
                    type: 'error',
                    message: 'Failed to copy to clipboard'
                });
            });
        } else {
            // Fallback method for older browsers or non-HTTPS
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            document.body.appendChild(textArea);
            textArea.select();
            
            try {
                document.execCommand('copy');
                window.Livewire.dispatch('toast', {
                    type: 'success',
                    message: 'Phone number copied to clipboard!'
                });
            } catch (err) {
                console.error('Fallback copy failed:', err);
                window.Livewire.dispatch('toast', {
                    type: 'error',
                    message: 'Failed to copy to clipboard'
                });
            }
            
            document.body.removeChild(textArea);
        }
    }
</script>
