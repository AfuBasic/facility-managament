<div class="p-6 space-y-6">
    {{-- Page Header --}}
    <x-ui.page-header title="Facilities" subtitle="Manage your facilities and locations">
        <x-slot:actions>
            @can('create facilities')
            <button wire:click="create" type="button" class="inline-flex items-center gap-x-2 rounded-xl bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-teal-500/30 hover:shadow-teal-500/50 hover:scale-105 transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Facility
            </button>
            @endcan
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Search Bar --}}
  <div class="mb-6">
    <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </div>
        <input 
            type="text" 
            wire:model.live.debounce.300ms="search" 
            class="block border w-full rounded-lg border-slate-300 pl-10 pr-3 py-2.5 text-slate-900 placeholder:text-slate-400 focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-colors"
            placeholder="Search facilities by name, address or contact person..."
        >
    </div>
</div>

    {{-- Facilities Grid --}}
    @if($facilities->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($facilities as $facility)
                <div class="group relative bg-white rounded-xl border border-slate-200 p-6 hover:shadow-lg hover:border-teal-300 transition-all duration-200">
                    {{-- Facility Info --}}
                    <div class="space-y-4">
                        {{-- Name --}}
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 group-hover:text-teal-600 transition-colors">
                                {{ $facility->name }}
                            </h3>
                            @if($facility->address)
                                <p class="mt-1 text-sm text-slate-600 flex items-start gap-2">
                                    <svg class="h-4 w-4 mt-0.5 flex-shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    <span>{{ $facility->address }}</span>
                                </p>
                            @endif
                        </div>

                        {{-- Contact Person --}}
                        @if($facility->contact_person_name || $facility->contact_person_phone)
                            <div class="pt-3 border-t border-slate-100">
                                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Contact Person</p>
                                @if($facility->contact_person_name)
                                    <p class="text-sm text-slate-700 flex items-center gap-2">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                        {{ $facility->contact_person_name }}
                                    </p>
                                @endif
                                @if($facility->contact_person_phone)
                                    <p class="text-sm text-slate-700 flex items-center gap-2 mt-1">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                        </svg>
                                        {{ $facility->contact_person_phone }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        {{-- Assigned Users Count --}}
                        <div class="pt-3 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Assigned Users</span>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-50 px-2.5 py-1 text-xs font-medium text-teal-700">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    {{ $facility->users_count }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-6 flex items-center gap-2">
                        @can('manage facilities')
                        <a href="{{ route('app.facilities.show', $facility) }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-3 py-2 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Manage
                        </a>
                        @endcan
                        @can('edit facilities')
                            <button wire:click="edit({{ $facility->id }})" class="@cannot('manage facilities') flex-1 @endcannot inline-flex items-center justify-center rounded-lg bg-slate-100 p-2 text-slate-700 hover:bg-slate-200 transition-all">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                        @endcan
                        
                        @can('delete facilities')
                            <button 
                                @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                    detail: {
                                        title: 'Delete Facility',
                                        message: 'Are you sure you want to delete this facility? This will also delete all associated spaces and data.',
                                        confirmText: 'Delete Facility',
                                        cancelText: 'Cancel',
                                        variant: 'danger',
                                        action: () => $wire.delete({{ $facility->id }})
                                    }
                                }))"
                                class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 transition-all"
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

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $facilities->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">
                @if($search)
                    No facilities found
                @else
                    No facilities yet
                @endif
            </h3>
            <p class="text-sm text-slate-500 mb-6">
                @if($search)
                    Try adjusting your search criteria
                @else
                    Get started by creating your first facility
                @endif
            </p>
            @if(!$search)
                @can('create facilities')
                    <button wire:click="create" type="button" class="inline-flex items-center gap-x-2 rounded-xl bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-teal-500/30 hover:shadow-teal-500/50 hover:scale-105 transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Your First Facility
                    </button>
                @endcan
            @endif
        </div>
    @endif

    {{-- Create/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity -z-10" wire:click="closeModal"></div>

                {{-- Modal panel --}}
                <div class="relative inline-block align-bottom bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl border border-slate-700 px-6 pt-5 pb-6 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-8 z-10">
                    <div class="space-y-6">
                        {{-- Header --}}
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-white">
                                {{ $isEditing ? 'Edit Facility' : 'Create New Facility' }}
                            </h3>
                            <button wire:click="closeModal" class="text-slate-400 hover:text-white transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Form --}}
                        <form wire:submit="save" class="space-y-5">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-300 mb-2">
                                    Facility Name <span class="text-red-400">*</span>
                                </label>
                                <input 
                                    wire:model="name" 
                                    type="text" 
                                    id="name"
                                    class="w-full rounded-md border border-slate-600 bg-slate-900/50 px-4 py-2.5 text-sm text-white placeholder-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all"
                                    placeholder="Enter facility name"
                                />
                                @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>

                            {{-- Address --}}
                            <div>
                                <label for="address" class="block text-sm font-medium text-slate-300 mb-2">
                                    Address
                                </label>
                                <textarea 
                                    wire:model="address" 
                                    id="address"
                                    rows="2"
                                    class="w-full rounded-md border border-slate-600 bg-slate-900/50 px-4 py-2.5 text-sm text-white placeholder-slate-400 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all resize-none"
                                    placeholder="Enter facility address"
                                ></textarea>
                                @error('address') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-3 pt-4">
                                <button 
                                    type="submit"
                                    class="flex-1 inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-teal-500/30 hover:shadow-teal-500/50 hover:scale-105 transition-all duration-200"
                                >
                                    {{ $isEditing ? 'Update Facility' : 'Create Facility' }}
                                </button>
                                <button 
                                    type="button"
                                    wire:click="closeModal"
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
