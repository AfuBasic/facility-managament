<div class="p-6 space-y-6">
    {{-- Facility Header --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="flex items-start justify-between">
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
                <div class="ml-6 bg-slate-50 rounded-lg p-4 min-w-[250px]">
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
        <div class="border-b border-slate-200">
            <nav class="flex gap-8 px-6" aria-label="Tabs">
                <button 
                    wire:click="setTab('spaces')" 
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'spaces' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
                >
                    Spaces
                </button>
                <button 
                    wire:click="setTab('assets')" 
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'assets' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
                >
                    Assets
                </button>
                <button 
                    wire:click="setTab('consumables')" 
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'consumables' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
                >
                    Consumables
                </button>
                <button 
                    wire:click="setTab('managers')" 
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'managers' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
                >
                    Managers
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-6">
            @if($activeTab === 'spaces')
                {{-- Spaces Tab --}}
                <div class="space-y-4">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-slate-900">Spaces</h3>
                        @can('create facilities')
                            <button wire:click="createSpace" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Space
                            </button>
                        @endcan
                    </div>

                    @if($facility->spaces->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($facility->spaces as $space)
                                <div class="bg-slate-50 rounded-lg border border-slate-200 p-4 hover:border-teal-300 transition-all">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-slate-900">{{ $space->name }}</h4>
                                            @if($space->type)
                                                <p class="text-xs text-slate-500 mt-1">{{ $space->type }}</p>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $space->status === 'active' ? 'bg-green-50 text-green-700' : ($space->status === 'maintenance' ? 'bg-yellow-50 text-yellow-700' : 'bg-slate-100 text-slate-700') }}">
                                            {{ ucfirst($space->status) }}
                                        </span>
                                    </div>

                                    <div class="space-y-2 text-sm text-slate-600">
                                        @if($space->floor)
                                            <p class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                                                </svg>
                                                Floor: {{ $space->floor }}
                                            </p>
                                        @endif
                                        @if($space->area)
                                            <p class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                                                </svg>
                                                {{ number_format($space->area, 2) }} m²
                                            </p>
                                        @endif
                                        @if($space->capacity)
                                            <p class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                                </svg>
                                                Capacity: {{ $space->capacity }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-200">
                                        @can('edit facilities')
                                            <button wire:click="editSpace({{ $space->id }})" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-white border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-all">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                                Edit
                                            </button>
                                        @endcan
                                        @can('delete facilities')
                                            <button 
                                                @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                                    detail: {
                                                        title: 'Delete Space',
                                                        message: 'Are you sure you want to delete this space? This action cannot be undone.',
                                                        confirmText: 'Delete Space',
                                                        cancelText: 'Cancel',
                                                        variant: 'danger',
                                                        action: () => $wire.deleteSpace({{ $space->id }})
                                                    }
                                                }))"
                                                class="inline-flex items-center justify-center rounded-lg bg-red-50 p-1.5 text-red-600 hover:bg-red-100 transition-all"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-700 mb-2">No spaces yet</h3>
                            <p class="text-sm text-slate-500 mb-6">Get started by creating your first space</p>
                            @can('create facilities')
                                <button wire:click="createSpace" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Add Your First Space
                                </button>
                            @endcan
                        </div>
                    @endif
                </div>
            @elseif($activeTab === 'assets')
                <div class="text-center py-12">
                    <p class="text-slate-500">Assets management coming soon...</p>
                </div>
            @elseif($activeTab === 'consumables')
                <div class="text-center py-12">
                    <p class="text-slate-500">Consumables management coming soon...</p>
                </div>
            @elseif($activeTab === 'managers')
                <div class="text-center py-12">
                    <p class="text-slate-500">Managers management coming soon...</p>
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

                            <div class="grid grid-cols-3 gap-4">
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
                                    <label for="spaceArea" class="block text-sm font-medium text-slate-300 mb-2">
                                        Area (m²)
                                    </label>
                                    <input 
                                        wire:model="spaceArea" 
                                        type="number" 
                                        step="0.01"
                                        id="spaceArea"
                                        class="w-full rounded-md border border-slate-600 bg-slate-900/50 px-4 py-2.5 text-sm text-white placeholder-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all"
                                        placeholder="0.00"
                                    />
                                    @error('spaceArea') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="spaceCapacity" class="block text-sm font-medium text-slate-300 mb-2">
                                        Capacity
                                    </label>
                                    <input 
                                        wire:model="spaceCapacity" 
                                        type="number" 
                                        id="spaceCapacity"
                                        class="w-full rounded-md border border-slate-600 bg-slate-900/50 px-4 py-2.5 text-sm text-white placeholder-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all"
                                        placeholder="0"
                                    />
                                    @error('spaceCapacity') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                                </div>
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
