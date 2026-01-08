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
                @include('livewire.client.facility-detail.tabs.spaces')
            @elseif($activeTab === 'assets' && auth()->user()->can('view assets'))
                @include('livewire.client.facility-detail.tabs.assets')
            @elseif($activeTab === 'consumables' && auth()->user()->can('view consumables'))
                @include('livewire.client.facility-detail.tabs.consumables')
            @elseif($activeTab === 'managers' && auth()->user()->can('view users'))
                @include('livewire.client.facility-detail.tabs.managers')
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

    {{-- Space Modal --}}
    @if($showSpaceModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity -z-10" wire:click="closeSpaceModal"></div>

                <div class="relative inline-block align-bottom bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl border border-slate-700 px-6 pt-5 pb-6 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-8 z-10">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-white">
                                {{ $isEditingSpace ? 'Edit Space' : 'Create New Space' }}
                            </h3>
                            <button wire:click="closeSpaceModal" class="text-slate-400 hover:text-white transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form wire:submit="saveSpace" class="space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="spaceName" class="block text-sm font-medium text-slate-300 mb-2">
                                        Space Name <span class="text-red-400">*</span>
                                    </label>
                                    <input 
                                        wire:model="spaceName" 
                                        type="text" 
                                        id="spaceName"
                                        class="w-full rounded-md border border-slate-600 bg-slate-900/50 px-4 py-2.5 text-sm text-white placeholder-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all"
                                        placeholder="e.g., Conference Room A"
                                    />
                                    @error('spaceName') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="spaceType" class="block text-sm font-medium text-slate-300 mb-2">
                                        Type
                                    </label>
                                    <input 
                                        wire:model="spaceType" 
                                        type="text" 
                                        id="spaceType"
                                        class="w-full rounded-md border border-slate-600 bg-slate-900/50 px-4 py-2.5 text-sm text-white placeholder-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all"
                                        placeholder="e.g., Office, Storage"
                                    />
                                    @error('spaceType') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="spaceFloor" class="block text-sm font-medium text-slate-300 mb-2">
                                        Floor
                                    </label>
                                    <input 
                                        wire:model="spaceFloor" 
                                        type="text" 
                                        id="spaceFloor"
                                        class="w-full rounded-md border border-slate-600 bg-slate-900/50 px-4 py-2.5 text-sm text-white placeholder-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all"
                                        placeholder="e.g., 2nd"
                                    />
                                    @error('spaceFloor') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                <label for="spaceStatus" class="block text-sm font-medium text-slate-300 mb-2">
                                    Status <span class="text-red-400">*</span>
                                </label>
                                <select 
                                    wire:model="spaceStatus" 
                                    id="spaceStatus"
                                    class="w-full rounded-md border border-slate-600 bg-slate-900/50 px-4 py-2.5 text-sm text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('spaceStatus') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>
                            </div>

                            <div>
                                <label for="spaceDescription" class="block text-sm font-medium text-slate-300 mb-2">
                                    Description
                                </label>
                                <textarea 
                                    wire:model="spaceDescription" 
                                    id="spaceDescription"
                                    rows="3"
                                    class="w-full rounded-md border border-slate-600 bg-slate-900/50 px-4 py-2.5 text-sm text-white placeholder-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all resize-none"
                                    placeholder="Enter space description..."
                                ></textarea>
                                @error('spaceDescription') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center gap-3 pt-4">
                                <button 
                                    type="submit"
                                    class="flex-1 inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-teal-500/30 hover:shadow-teal-500/50 hover:scale-105 transition-all duration-200"
                                >
                                    {{ $isEditingSpace ? 'Update Space' : 'Create Space' }}
                                </button>
                                <button 
                                    type="button"
                                    wire:click="closeSpaceModal"
                                    class="px-4 py-2.5 text-sm font-medium text-slate-400 hover:text-white transition-colors"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- View Space Modal --}}
    @if($showViewSpaceModal && $viewingSpace)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity" wire:click="closeViewSpaceModal"></div>

                <div class="relative inline-block align-bottom bg-white rounded-2xl border border-slate-200 px-6 pt-5 pb-6 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-8">
                    <div class="space-y-6">
                        {{-- Header --}}
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-slate-900">{{ $viewingSpace->name }}</h3>
                                @if($viewingSpace->type)
                                    <p class="text-sm text-slate-500 mt-1">{{ $viewingSpace->type }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $viewingSpace->status === 'active' ? 'bg-green-50 text-green-700 border border-green-200' : ($viewingSpace->status === 'maintenance' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                    {{ ucfirst($viewingSpace->status) }}
                                </span>
                                <button wire:click="closeViewSpaceModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Details Grid --}}
                        <div class="grid grid-cols-2 gap-6">
                            @if($viewingSpace->floor)
                                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center">
                                            <svg class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Floor</p>
                                            <p class="text-lg font-semibold text-slate-900 mt-1">{{ $viewingSpace->floor }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($viewingSpace->area)
                                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Area</p>
                                            <p class="text-lg font-semibold text-slate-900 mt-1">{{ number_format($viewingSpace->area, 2) }} m²</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($viewingSpace->capacity)
                                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Capacity</p>
                                            <p class="text-lg font-semibold text-slate-900 mt-1">{{ $viewingSpace->capacity }} {{ $viewingSpace->capacity == 1 ? 'person' : 'people' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Description --}}
                        @if($viewingSpace->description)
                            <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Description</p>
                                <p class="text-sm text-slate-700 leading-relaxed">{{ $viewingSpace->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
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
