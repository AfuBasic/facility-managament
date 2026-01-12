<div class="space-y-6">
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Assets --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Total Assets</p>
                <div class="flex items-end gap-2">
                    <h3 class="text-3xl font-bold text-slate-900">{{ $this->totalAssets }}</h3>
                    <span class="text-sm text-slate-500 mb-1">items</span>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-sm text-slate-600">
                <div class="p-2 bg-teal-50 rounded-lg">
                    <x-heroicon-o-cube class="h-5 w-5 text-teal-600" />
                </div>
                <span>In inventory</span>
            </div>
        </div>

        {{-- Low Stock Alerts --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Low Stock Alerts</p>
                <div class="flex items-end gap-2">
                    <h3 class="text-3xl font-bold {{ $this->lowStockAssets->count() > 0 ? 'text-amber-500' : 'text-slate-900' }}">
                        {{ $this->lowStockAssets->count() }}
                    </h3>
                    <span class="text-sm text-slate-500 mb-1">items</span>
                </div>
            </div>
            @if($this->lowStockAssets->count() > 0)
                <div class="mt-4 flex items-center gap-2 text-sm text-amber-700 bg-amber-50 px-3 py-2 rounded-lg">
                    <x-heroicon-o-exclamation-triangle class="h-4 w-4 shrink-0" />
                    <span class="truncate">Restock needed soon</span>
                </div>
            @else
                <div class="mt-4 flex items-center gap-2 text-sm text-green-700 bg-green-50 px-3 py-2 rounded-lg">
                    <x-heroicon-o-check-circle class="h-4 w-4 shrink-0" />
                    <span>Stock levels healthy</span>
                </div>
            @endif
        </div>

        {{-- Categories --}}
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">Asset Types</p>
            <div class="space-y-3">
                @foreach($this->assetsByType as $type => $count)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-slate-600 capitalize">{{ $type }}</span>
                            <span class="font-medium text-slate-900">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5">
                            <div 
                                class="h-1.5 rounded-full {{ $type === 'fixed' ? 'bg-blue-500' : ($type === 'tools' ? 'bg-purple-500' : 'bg-orange-500') }}"
                                style="width: {{ ($count / max(1, $this->totalAssets)) * 100 }}%"
                            ></div>
                        </div>
                    </div>
                @endforeach
                @if($this->assetsByType->isEmpty())
                    <p class="text-sm text-slate-400 italic">No assets categorized yet.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Activity --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="font-semibold text-slate-900 mb-4">Recent Activity</h3>
            
            <div class="relative">
                @if($this->recentActivity->count() > 0)
                    {{-- Timeline Line --}}
                    <div class="absolute left-6 top-0 bottom-0 w-px bg-slate-200"></div>

                    <div class="space-y-6">
                        @foreach($this->recentActivity as $asset)
                            <div class="relative flex gap-4">
                                {{-- Timeline Dot --}}
                                <div class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white border border-slate-200">
                                    @if($asset->type === 'fixed')
                                        <x-heroicon-o-computer-desktop class="h-5 w-5 text-blue-500" />
                                    @elseif($asset->type === 'tools')
                                        <x-heroicon-o-wrench-screwdriver class="h-5 w-5 text-purple-500" />
                                    @elseif($asset->type === 'consumable')
                                        <x-heroicon-o-archive-box class="h-5 w-5 text-orange-500" />
                                    @endif
                                </div>
                                
                                <div class="flex-1 py-1">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 mb-1">
                                        <h4 class="font-medium text-slate-900">{{ $asset->name }}</h4>
                                        <span class="text-xs text-slate-500">{{ $asset->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-slate-600 mb-2">
                                        Added by <span class="font-medium text-slate-700">{{ $asset->user->name ?? 'System' }}</span>
                                        &bull; <span class="capitalize">{{ $asset->type }}</span>
                                        &bull; {{ $asset->units }} units
                                    </p>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center rounded-md bg-slate-50 px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                            SN: {{ $asset->serial }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                            <x-heroicon-o-clock class="h-6 w-6 text-slate-400" />
                        </div>
                        <p class="text-slate-500">No recent activity found.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Store Info / Quick Actions --}}
        <div class="space-y-6">
            {{-- Manager Card --}}
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-900 mb-4">Store Manager</h3>
                @if($store->storeManager)
                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-12 w-12 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold text-lg">
                            {{ substr($store->storeManager->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">{{ $store->storeManager->name }}</p>
                            <p class="text-sm text-slate-500">{{ $store->storeManager->email }}</p>
                        </div>
                    </div>
                    @if($store->storeManager->phone)
                        <div class="flex items-center gap-2 text-sm text-slate-600 mb-4">
                            <x-heroicon-o-phone class="h-4 w-4" />
                            {{ $store->storeManager->phone }}
                        </div>
                    @endif
                @else
                    <div class="text-sm text-slate-500 italic mb-4">No manager assigned</div>
                @endif
                
                <hr class="border-slate-100 my-4" />
                
                <h4 class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">Quick Actions</h4>
                <div class="space-y-2">
                    <button 
                        wire:click="$parent.setTab('assets')"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 rounded-lg transition-colors text-left"
                    >
                        <x-heroicon-o-list-bullet class="h-4 w-4 text-slate-400" />
                        View All Assets
                    </button>
                    {{-- Note: 'Add Asset' usually requires opening the modal in StoreAssets. 
                         Cross-component communication might be needed if we want a direct 'Add' button here. 
                         For now, we route them to the assets tab. --}}
                </div>
            </div>
        </div>
    </div>
</div>
