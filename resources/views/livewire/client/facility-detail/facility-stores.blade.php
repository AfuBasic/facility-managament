<div class="space-y-4">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-slate-900">Stores</h3>
        @can('create stores')
            <button wire:click="createStore" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Store
            </button>
        @endcan
    </div>

    @if($facility->stores->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($facility->stores as $store)
                <div class="bg-slate-50 rounded-lg border border-slate-200 p-4 hover:border-teal-300 transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="font-semibold text-slate-900">{{ $store->name }}</h4>
                            @if($store->storeManager)
                                <p class="text-xs text-slate-500 mt-1">Manager: {{ $store->storeManager->name }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $store->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-700' }}">
                            {{ ucfirst($store->status) }}
                        </span>
                    </div>

                    @if($store->description)
                        <p class="text-sm text-slate-600 mb-4">{{ Str::limit($store->description, 100) }}</p>
                    @endif

                    <div class="flex items-center gap-2 pt-4 border-t border-slate-200">
                        @can('view stores')
                            <a 
                                href="{{ route('app.stores.detail', $store->hashid) }}" 
                                wire:navigate
                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all"
                            >
                                <x-heroicon-o-arrow-right class="h-4 w-4" />
                                Manage Store
                            </a>
                        @endcan
                        @can('edit stores')
                            <button 
                                wire:click="editStore({{ $store->id }})" 
                                class="inline-flex items-center justify-center rounded-lg bg-slate-50 p-2 text-slate-600 hover:bg-slate-100 transition-all"
                                title="Edit Store"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                Edit
                            </button>
                        @endcan
                        @can('delete stores')
                            <button 
                                @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                    detail: {
                                        title: 'Delete Store',
                                        message: 'Are you sure you want to delete this store? This action cannot be undone.',
                                        confirmText: 'Delete Store',
                                        cancelText: 'Cancel',
                                        variant: 'danger',
                                        action: () => $wire.deleteStore({{ $store->id }})
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
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">No stores yet</h3>
            <p class="text-sm text-slate-500 mb-6">Get started by creating your first store</p>
            @can('create stores')
                <button wire:click="createStore" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Your First Store
                </button>
            @endcan
        </div>
    @endif

    {{-- Store Modal --}}
    @if($showStoreModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity" wire:click="closeStoreModal"></div>

                <div class="relative inline-block align-bottom bg-white rounded-2xl border border-slate-200 px-6 pt-5 pb-6 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-8">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-slate-900">
                                {{ $isEditingStore ? 'Edit Store' : 'Create New Store' }}
                            </h3>
                            <button wire:click="closeStoreModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form wire:submit="saveStore" class="space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="storeName" class="block text-sm font-medium text-slate-700 mb-2">
                                        Store Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        wire:model="storeName" 
                                        type="text" 
                                        id="storeName"
                                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                        placeholder="e.g., Main Store"
                                    />
                                    @error('storeName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="storeStatus" class="block text-sm font-medium text-slate-700 mb-2">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        wire:model="storeStatus" 
                                        id="storeStatus"
                                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                                    >
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    @error('storeStatus') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                @php
                                    $managerOptions = [];
                                    foreach($this->availableManagers as $membership) {
                                        $managerOptions[$membership->user->id] = ($membership->user->name ?? 'New User') . ' • ' . $membership->user->email;
                                    }
                                @endphp
                                
                                <x-forms.searchable-select
                                    wire:model="storeManagerId"
                                    :options="$managerOptions"
                                    :selected="$storeManagerId"
                                    label="Store Manager"
                                    placeholder="Select a manager..."
                                    :error="$errors->first('storeManagerId')"
                                />
                            </div>

                            <div>
                                <label for="storeDescription" class="block text-sm font-medium text-slate-700 mb-2">
                                    Description
                                </label>
                                <textarea 
                                    wire:model="storeDescription" 
                                    id="storeDescription"
                                    rows="3"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 resize-none"
                                    placeholder="Enter store description..."
                                ></textarea>
                                @error('storeDescription') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center gap-3 pt-4">
                                <button 
                                    type="submit"
                                    class="flex-1 inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all"
                                >
                                    {{ $isEditingStore ? 'Update Store' : 'Create Store' }}
                                </button>
                                <button 
                                    type="button"
                                    wire:click="closeStoreModal"
                                    class="px-4 py-2.5 text-sm font-medium text-slate-700 hover:text-slate-900 transition-colors"
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
