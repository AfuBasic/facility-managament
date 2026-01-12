<div class="p-2 space-y-2 md:p-6 md:space-y-6">
    {{-- Store Header --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="flex flex-col gap-5 md:flex-row items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('app.facilities.show', $store->facility->hashid) }}?tab=stores" wire:navigate class="text-slate-400 hover:text-slate-600 transition-colors">
                        <x-heroicon-o-arrow-left class="h-5 w-5" />
                    </a>
                    <h1 class="text-2xl font-bold text-slate-900">{{ $store->name }}</h1>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $store->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-800' }}">
                        {{ ucfirst($store->status) }}
                    </span>
                </div>
                
                <p class="text-sm text-slate-600">
                    <span class="font-medium">Facility:</span> {{ $store->facility->name }}
                </p>

                @if($store->description)
                    <p class="text-sm text-slate-600 mt-2">{{ $store->description }}</p>
                @endif

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2 mt-4">
                    @can('edit stores')
                        <a 
                            href="{{ route('app.facilities.show', $store->facility->hashid) }}?tab=stores&editStore={{ $store->hashid }}"
                            wire:navigate
                            class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 transition-all"
                        >
                            <x-heroicon-o-pencil class="h-4 w-4" />
                            Edit Store
                        </a>
                    @endcan
                    
                    @can('delete stores')
                        <button 
                            @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                detail: {
                                    title: 'Delete Store',
                                    message: 'Are you sure you want to delete this store? This action cannot be undone.',
                                    confirmText: 'Delete',
                                    cancelText: 'Cancel',
                                    variant: 'danger',
                                    action: () => $wire.deleteStore()
                                }
                            }))"
                            class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition-all"
                        >
                            <x-heroicon-o-trash class="h-4 w-4" />
                            Delete Store
                        </button>
                    @endcan
                </div>
            </div>

            @if($store->storeManager)
                <div class="md:ml-6 bg-slate-50 rounded-lg p-4 min-w-full md:min-w-[250px]">
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">Store Manager</p>
                    <p class="text-sm text-slate-700 flex items-center gap-2">
                        <x-heroicon-o-user class="h-4 w-4 text-slate-400" />
                        {{ $store->storeManager->name }}
                    </p>
                    <p class="text-sm text-slate-600 mt-1">{{ $store->storeManager->email }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-xl border border-slate-200">
        {{-- Tab Headers --}}
        <div class="border-b border-slate-200">
            <nav class="-mb-px flex gap-6 px-6" aria-label="Tabs">
                <button 
                    wire:click="setTab('overview')"
                    class="border-b-2 py-4 px-1 text-sm font-medium transition-colors {{ $activeTab === 'overview' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}"
                >
                    Overview
                </button>
                <button 
                    wire:click="setTab('assets')"
                    class="border-b-2 py-4 px-1 text-sm font-medium transition-colors {{ $activeTab === 'assets' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}"
                >
                    Assets
                </button>
                <button 
                    wire:click="setTab('activity')"
                    class="border-b-2 py-4 px-1 text-sm font-medium transition-colors {{ $activeTab === 'activity' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}"
                >
                    Activity Logs
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-6">
            @if($activeTab === 'overview')
                <livewire:client.store-detail.store-overview :store="$store" :clientAccount="$clientAccount" :key="'overview-'.$store->id" />
            @elseif($activeTab === 'assets')
                <livewire:client.store-detail.store-assets :store="$store" :clientAccount="$clientAccount" :key="'assets-'.$store->id" />
            @elseif($activeTab === 'activity')
                <livewire:client.store-detail.store-activity-log :store="$store" :clientAccount="$clientAccount" :key="'activity-'.$store->id" />
            @endif
        </div>
    </div>

    {{-- Global Modals --}}
    <livewire:client.store-detail.view-asset-modal />
</div>
