<div class="space-y-6">
    {{-- Header with Add Button --}}
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-900">Assets & Consumables</h3>
        @can('create assets')
            <button wire:click="createAsset" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Asset
            </button>
        @endcan
    </div>

    {{-- Filter Tabs and Search --}}
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        {{-- Type Filter Tabs --}}
        <div class="flex gap-2 overflow-x-auto">
            <button 
                wire:click="$set('filterType', 'all')"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filterType === 'all' ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                All
            </button>
            <button 
                wire:click="$set('filterType', 'fixed')"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filterType === 'fixed' ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                Fixed Assets
            </button>
            <button 
                wire:click="$set('filterType', 'tools')"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filterType === 'tools' ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                Tools
            </button>
            <button 
                wire:click="$set('filterType', 'consumable')"
                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filterType === 'consumable' ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
            >
                Consumables
            </button>
        </div>

        {{-- Search --}}
        <div class="relative w-full sm:w-64">
            <input 
                wire:model.live.debounce.300ms="search"
                type="text" 
                placeholder="Search assets..."
                class="w-full rounded-lg border border-slate-300 pl-10 pr-4 py-2 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
            />
            <svg class="absolute left-3 top-2.5 h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
        </div>
    </div>

    {{-- Assets Grid --}}
    @if($this->assets->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($this->assets as $asset)
                <div class="bg-white rounded-lg border border-slate-200 p-4 hover:border-teal-300 hover:shadow-md transition-all">
                    {{-- Asset Image --}}
                    @if($asset->images->count() > 0)
                        <div class="mb-3 rounded-lg overflow-hidden bg-slate-100 h-40">
                            <img src="{{ $asset->images->first()->image }}" alt="{{ $asset->name }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="mb-3 rounded-lg bg-slate-100 h-40 flex items-center justify-center">
                            <svg class="h-16 w-16 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                        </div>
                    @endif

                    {{-- Asset Info --}}
                    <div class="space-y-2">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-slate-900">{{ $asset->name }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Serial: {{ $asset->serial }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium 
                                {{ $asset->type === 'fixed' ? 'bg-blue-50 text-blue-700' : '' }}
                                {{ $asset->type === 'tools' ? 'bg-purple-50 text-purple-700' : '' }}
                                {{ $asset->type === 'consumable' ? 'bg-orange-50 text-orange-700' : '' }}
                            ">
                                {{ ucfirst($asset->type) }}
                            </span>
                        </div>

                        @if($asset->description)
                            <p class="text-sm text-slate-600 line-clamp-2">{{ $asset->description }}</p>
                        @endif

                        {{-- Units Info --}}
                        <div class="flex items-center gap-4 text-xs text-slate-600">
                            <div>
                                <span class="font-medium">Units:</span> {{ $asset->units }}
                            </div>
                            <div>
                                <span class="font-medium">Min:</span> {{ $asset->minimum }}
                            </div>
                            <div>
                                <span class="font-medium">Max:</span> {{ $asset->maximum }}
                            </div>
                        </div>

                        {{-- Additional Info --}}
                        <div class="space-y-1 text-xs text-slate-500">
                            @if($asset->store)
                                <div class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                                    </svg>
                                    Store: {{ $asset->store->name }}
                                </div>
                            @endif
                            @if($asset->user)
                                <div class="flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    Assigned: {{ $asset->user->name }}
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 pt-3 border-t border-slate-200">
                            @can('edit assets')
                                <button 
                                    wire:click="editAsset({{ $asset->id }})" 
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 transition-all"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                    Edit
                                </button>
                            @endcan
                            @can('delete assets')
                                <button 
                                    @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                        detail: {
                                            title: 'Delete Asset',
                                            message: 'Are you sure you want to delete this asset? This action cannot be undone.',
                                            confirmText: 'Delete Asset',
                                            cancelText: 'Cancel',
                                            variant: 'danger',
                                            action: () => $wire.deleteAsset({{ $asset->id }})
                                        }
                                    }))"
                                    class="inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-600 hover:bg-red-100 transition-all"
                                    title="Delete Asset"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">
                @if($search || $filterType !== 'all')
                    No assets found
                @else
                    No assets yet
                @endif
            </h3>
            <p class="text-sm text-slate-500 mb-6">
                @if($search || $filterType !== 'all')
                    Try adjusting your filters or search terms
                @else
                    Get started by creating your first asset
                @endif
            </p>
            @if(!$search && $filterType === 'all')
                @can('create assets')
                    <button wire:click="createAsset" class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Your First Asset
                    </button>
                @endcan
            @endif
        </div>
    @endif


    {{-- Asset Modal --}}
    <x-ui.modal show="showAssetModal" title="{{ $isEditingAsset ? 'Edit Asset' : 'Create New Asset' }}" maxWidth="4xl">
        <form wire:submit="saveAsset" class="space-y-5">
            {{-- Basic Info --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="assetName" class="block text-sm font-medium text-slate-700 mb-2">
                        Asset Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        wire:model="assetName" 
                        type="text" 
                        id="assetName"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                        placeholder="e.g., Laptop Dell XPS 15"
                    />
                    @error('assetName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="assetSerial" class="block text-sm font-medium text-slate-700 mb-2">
                        Serial Number <span class="text-red-500">*</span>
                    </label>
                    <input 
                        wire:model="assetSerial" 
                        type="text" 
                        id="assetSerial"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                        placeholder="e.g., SN123456789"
                    />
                    @error('assetSerial') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Type and Units --}}
            <div class="grid grid-cols-4 gap-4">
                <div>
                    <label for="assetType" class="block text-sm font-medium text-slate-700 mb-2">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <select 
                        wire:model="assetType" 
                        id="assetType"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                    >
                        <option value="fixed">Fixed Asset</option>
                        <option value="tools">Tools</option>
                        <option value="consumable">Consumable</option>
                    </select>
                    @error('assetType') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="assetUnits" class="block text-sm font-medium text-slate-700 mb-2">
                        Units <span class="text-red-500">*</span>
                    </label>
                    <input 
                        wire:model="assetUnits" 
                        type="number" 
                        id="assetUnits"
                        min="0"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                    />
                    @error('assetUnits') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="assetMinimum" class="block text-sm font-medium text-slate-700 mb-2">
                        Minimum <span class="text-red-500">*</span>
                    </label>
                    <input 
                        wire:model="assetMinimum" 
                        type="number" 
                        id="assetMinimum"
                        min="0"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                    />
                    @error('assetMinimum') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="assetMaximum" class="block text-sm font-medium text-slate-700 mb-2">
                        Maximum <span class="text-red-500">*</span>
                    </label>
                    <input 
                        wire:model="assetMaximum" 
                        type="number" 
                        id="assetMaximum"
                        min="0"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                    />
                    @error('assetMaximum') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Assignments --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    @php
                        $storeOptions = [];
                        foreach($this->availableStores as $store) {
                            $storeOptions[$store->id] = $store->name;
                        }
                    @endphp
                    
                    <x-forms.searchable-select
                        wire:model="assetStoreId"
                        :options="$storeOptions"
                        :selected="$assetStoreId"
                        label="Store"
                        placeholder="Select a store..."
                        :error="$errors->first('assetStoreId')"
                    />
                </div>

                <div>
                    @php
                        $userOptions = [];
                        foreach($this->availableUsers as $membership) {
                            $userOptions[$membership->user->id] = ($membership->user->name ?? 'New User') . ' • ' . $membership->user->email;
                        }
                    @endphp
                    
                    <x-forms.searchable-select
                        wire:model="assetUserId"
                        :options="$userOptions"
                        :selected="$assetUserId"
                        label="Assigned User"
                        placeholder="Select a user..."
                        :error="$errors->first('assetUserId')"
                    />
                </div>
            </div>

            {{-- More Details --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    @php
                        $contactOptions = [];
                        foreach($this->availableContacts as $contact) {
                            $contactOptions[$contact->id] = $contact->name . ' • ' . $contact->email;
                        }
                    @endphp
                    
                    <x-forms.searchable-select
                        wire:model="assetSupplierContactId"
                        :options="$contactOptions"
                        :selected="$assetSupplierContactId"
                        label="Supplier Contact"
                        placeholder="Select a supplier..."
                        :error="$errors->first('assetSupplierContactId')"
                    />
                </div>

                <div>
                    @php
                        $spaceOptions = [];
                        foreach($this->availableSpaces as $space) {
                            $spaceOptions[$space->id] = $space->name;
                        }
                    @endphp
                    
                    <x-forms.searchable-select
                        wire:model="assetSpaceId"
                        :options="$spaceOptions"
                        :selected="$assetSpaceId"
                        label="Space/Location"
                        placeholder="Select a space..."
                        :error="$errors->first('assetSpaceId')"
                    />
                </div>
            </div>

            {{-- Purchase Date --}}
            <div>
                <label for="assetPurchasedAt" class="block text-sm font-medium text-slate-700 mb-2">
                    Purchase Date
                </label>
                <input 
                    wire:model="assetPurchasedAt" 
                    type="date" 
                    id="assetPurchasedAt"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20"
                />
                @error('assetPurchasedAt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="assetDescription" class="block text-sm font-medium text-slate-700 mb-2">
                    Description
                </label>
                <textarea 
                    wire:model="assetDescription" 
                    id="assetDescription"
                    rows="2"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 resize-none"
                    placeholder="Enter asset description..."
                ></textarea>
                @error('assetDescription') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label for="assetNotes" class="block text-sm font-medium text-slate-700 mb-2">
                    Notes
                </label>
                <textarea 
                    wire:model="assetNotes" 
                    id="assetNotes"
                    rows="2"
                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 resize-none"
                    placeholder="Enter additional notes..."
                ></textarea>
                @error('assetNotes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Image Upload --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Images (Max 5)
                </label>
                
                {{-- Existing Images --}}
                @if(count($existingImages) > 0)
                    <div class="grid grid-cols-5 gap-2 mb-3">
                        @foreach($existingImages as $index => $image)
                            <div class="relative group">
                                <img src="{{ $image['url'] }}" class="w-full h-24 object-cover rounded-lg border border-slate-200">
                                <button 
                                    type="button"
                                    wire:click="deleteExistingImage({{ $image['id'] }})"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Upload Progress --}}
                @if(count($uploadedImages) > 0)
                    <div class="grid grid-cols-5 gap-2 mb-3">
                        @foreach($uploadedImages as $index => $image)
                            <div class="relative group">
                                <img src="{{ $image['url'] }}" class="w-full h-24 object-cover rounded-lg border border-slate-200">
                                <button 
                                    type="button"
                                    wire:click="removeUploadedImage({{ $index }})"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                @if($image['cached'] ?? false)
                                    <span class="absolute bottom-1 left-1 bg-green-500 text-white text-xs px-1.5 py-0.5 rounded">Cached</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Upload Input --}}
                @if(count($existingImages) + count($uploadedImages) < 5)
                    <div class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center hover:border-teal-400 transition-colors">
                        <input 
                            type="file" 
                            wire:model="photos" 
                            multiple 
                            accept="image/jpeg,image/jpg,image/png"
                            class="hidden" 
                            id="photoInput"
                        />
                        <label for="photoInput" class="cursor-pointer">
                            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-slate-600">
                                <span class="font-semibold text-teal-600">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-slate-500">PNG, JPG up to 2MB each</p>
                        </label>
                    </div>
                @endif

                @if($isUploading)
                    <div class="mt-2 flex items-center gap-2 text-sm text-teal-600">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading images...
                    </div>
                @endif

                @error('photos.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center gap-3 pt-4">
                <button 
                    type="submit"
                    @if($isUploading) disabled @endif
                    class="flex-1 inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    @if($isUploading)
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading...
                    @else
                        {{ $isEditingAsset ? 'Update Asset' : 'Create Asset' }}
                    @endif
                </button>
                <button 
                    type="button"
                    @click="show = false"
                    class="px-4 py-2.5 text-sm font-medium text-slate-700 hover:text-slate-900 transition-colors"
                >
                    Cancel
                </button>
            </div>
        </form>
    </x-ui.modal>
</div>
