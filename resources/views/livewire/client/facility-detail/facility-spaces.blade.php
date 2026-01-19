{{-- Spaces Tab --}}
<div class="space-y-4">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-slate-900">Spaces</h3>
        @can('create spaces')
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

                    <div class="space-y-2 text-sm text-slate-600 mb-4">
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

                    <div class="flex items-center gap-2 pt-4 border-t border-slate-200">
                        <button wire:click="viewSpace({{ $space->id }})" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-white border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-all">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            View
                        </button>
                        @can('edit spaces')
                            <button wire:click="editSpace({{ $space->id }})" class="inline-flex items-center justify-center rounded-lg bg-slate-100 p-1.5 text-slate-700 hover:bg-slate-200 transition-all">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                        @endcan
                        @can('delete spaces')
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
            @can('create spaces')
                <button wire:click="createSpace" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Your First Space
                </button>
            @endcan
        </div>
    @endif

    {{-- Space Modal --}}
    <x-ui.modal show="showSpaceModal" title="{{ $isEditingSpace ? 'Edit Space' : 'Create New Space' }}" maxWidth="2xl">
        <form wire:submit="saveSpace" class="space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="spaceName" class="block text-sm font-medium text-slate-700 mb-2">
                        Space Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        wire:model="spaceName" 
                        type="text" 
                        id="spaceName"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900  focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all"
                        placeholder="e.g., Conference Room A"
                    />
                    @error('spaceName') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="spaceType" class="block text-sm font-medium text-slate-700 mb-2">
                        Type
                    </label>
                    <input 
                        wire:model="spaceType" 
                        type="text" 
                        id="spaceType"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900  focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all"
                        placeholder="e.g., Office, Storage"
                    />
                    @error('spaceType') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="spaceFloor" class="block text-sm font-medium text-slate-700 mb-2">
                        Floor
                    </label>
                    <input 
                        wire:model="spaceFloor" 
                        type="text" 
                        id="spaceFloor"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900  focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all"
                        placeholder="e.g., 2nd"
                    />
                    @error('spaceFloor') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                <label for="spaceStatus" class="block text-sm font-medium text-slate-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select 
                    wire:model="spaceStatus" 
                    id="spaceStatus"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all"
                >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="maintenance">Maintenance</option>
                </select>
                @error('spaceStatus') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>
            </div>

            <div>
                <label for="spaceDescription" class="block text-sm font-medium text-slate-700 mb-2">
                    Description
                </label>
                <textarea 
                    wire:model="spaceDescription" 
                    id="spaceDescription"
                    rows="3"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900  focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all resize-none"
                    placeholder="Enter space description..."
                ></textarea>
                @error('spaceDescription') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button 
                    type="submit"
                    class="flex-1 inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-slate-900   transition-all duration-200"
                >
                    {{ $isEditingSpace ? 'Update Space' : 'Create Space' }}
                </button>
                <button 
                    type="button"
                    @click="show = false"
                    class="px-4 py-2.5 text-sm font-medium text-slate-400 hover:text-slate-900 transition-colors"
                >
                    Cancel
                </button>
            </div>
        </form>
    </x-ui.modal>

    {{-- View Space Modal --}}
    <x-ui.modal show="showViewSpaceModal" title="{{ $viewingSpace ? $viewingSpace->name : 'Space Details' }}" maxWidth="2xl">
        @if($viewingSpace)
            <div class="space-y-6">
                {{-- Header --}}
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        @if($viewingSpace->type)
                            <p class="text-sm text-slate-500 mt-1">{{ $viewingSpace->type }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $viewingSpace->status === 'active' ? 'bg-green-50 text-green-700 border border-green-200' : ($viewingSpace->status === 'maintenance' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                            {{ ucfirst($viewingSpace->status) }}
                        </span>
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
        @endif
    </x-ui.modal>
</div>
