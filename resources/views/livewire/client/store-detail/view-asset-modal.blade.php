<div x-data="{ open: @entangle('showModal') }"
     x-show="open"
     x-on:close-modal.window="open = false"
     class="relative z-50"
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
    
    {{-- Backdrop --}}
    <div x-show="open" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            
            {{-- Modal Panel --}}
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                
                @if($asset)
                    {{-- Header --}}
                    <div class="bg-white px-4 py-5 sm:px-6 border-b border-slate-200 flex justify-between items-center">
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-slate-900" id="modal-title">
                                {{ $asset->name }}
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-slate-500">
                                Serial: {{ $asset->serial }} &bull; <span class="capitalize">{{ $asset->type }}</span>
                            </p>
                        </div>
                        <button wire:click="closeModal" type="button" class="rounded-md bg-white text-slate-400 hover:text-slate-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <x-heroicon-o-x-mark class="h-6 w-6" />
                        </button>
                    </div>

                    {{-- Tabs --}}
                    <div class="border-b border-slate-200">
                        <nav class="-mb-px flex px-6 space-x-8" aria-label="Tabs">
                            <button wire:click="setTab('details')" 
                                    class="{{ $activeTab === 'details' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                                Details
                            </button>
                            <button wire:click="setTab('history')" 
                                    class="{{ $activeTab === 'history' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                                History
                            </button>
                        </nav>
                    </div>

                    {{-- Content --}}
                    <div class="px-4 py-5 sm:p-6 min-h-[300px]">
                        
                        {{-- Action Mode Interface --}}
                        @if($actionType)
                            <div class="bg-slate-50 rounded-lg p-4 border border-slate-200 mb-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-medium text-slate-900 capitalize">{{ $actionType }} Asset</h4>
                                    <button wire:click="setAction(null)" class="text-xs text-slate-500 hover:text-slate-700">Cancel</button>
                                </div>
                                
                                <div class="space-y-4">
                                    {{-- Quantity --}}
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700">Quantity</label>
                                        <input wire:model="quantity" type="number" min="1" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                        @error('quantity') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Restock specific --}}
                                    @if($actionType === 'restock')
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700">Cost per Unit (Optional)</label>
                                            <div class="relative mt-1 rounded-md shadow-sm">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <span class="text-slate-500 sm:text-sm">$</span>
                                                </div>
                                                <input wire:model="cost" type="number" step="0.01" class="block w-full rounded-md border-slate-300 pl-7 focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="0.00">
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Checkout specific --}}
                                    @if($actionType === 'checkout')
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700">Assign To</label>
                                            <select wire:model="targetUserId" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                                <option value="">Select User</option>
                                                @foreach($this->users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('targetUserId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                        </div>
                                    @endif
                                    
                                    {{-- Checkin specific --}}
                                    @if($actionType === 'checkin')
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700">Condition</label>
                                            <select wire:model="condition" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                                <option value="Good">Good</option>
                                                <option value="Damaged">Damaged</option>
                                                <option value="Needs Repair">Needs Repair</option>
                                            </select>
                                        </div>
                                    @endif

                                    {{-- Notes --}}
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700">Notes</label>
                                        <textarea wire:model="notes" rows="2" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"></textarea>
                                    </div>
                                    
                                    <div class="flex justify-end pt-2">
                                        <button wire:click="submitAction" class="bg-teal-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-teal-700">
                                            Confirm {{ ucfirst($actionType) }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Action Buttons --}}
                            <div class="flex gap-2 mb-6">
                                @can('edit assets')
                                    <button wire:click="setAction('restock')" class="flex-1 bg-white border border-slate-300 text-slate-700 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 flex justify-center items-center gap-2">
                                        <x-heroicon-o-plus class="h-4 w-4" /> Restock
                                    </button>
                                    
                                    @if($asset->units > 0)
                                        <button wire:click="setAction('checkout')" class="flex-1 bg-white border border-slate-300 text-slate-700 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 flex justify-center items-center gap-2">
                                            <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4" /> Check Out
                                        </button>
                                    @endif

                                    @if($asset->user_id && $asset->user_id !== Auth::id()) 
                                        {{-- Only show checkin if assigned to someone else (conceptually) --}}
                                        <button wire:click="setAction('checkin')" class="flex-1 bg-white border border-slate-300 text-slate-700 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 flex justify-center items-center gap-2">
                                            <x-heroicon-o-arrow-left-on-rectangle class="h-4 w-4" /> Return / Check In
                                        </button>
                                    @endif
                                @endcan
                            </div>
                        @endif

                        {{-- Tab Panels --}}
                        @if($activeTab === 'details')
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">Asset Name</dt>
                                    <dd class="mt-1 text-sm text-slate-900">{{ $asset->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">Serial Number</dt>
                                    <dd class="mt-1 text-sm text-slate-900 font-mono">{{ $asset->serial }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">Current Stock</dt>
                                    <dd class="mt-1 text-sm font-medium {{ $asset->units <= $asset->minimum ? 'text-red-600' : 'text-slate-900' }}">
                                        {{ $asset->units }} {{ $asset->units === 1 ? 'unit' : 'units' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-slate-500">Assigned To</dt>
                                    <dd class="mt-1 text-sm text-slate-900 flex items-center gap-1">
                                        @if($asset->user)
                                            <div class="h-5 w-5 rounded-full bg-slate-200 flex items-center justify-center text-xs">
                                                {{ substr($asset->user->name, 0, 1) }}
                                            </div>
                                            {{ $asset->user->name }}
                                        @else
                                            <span class="text-slate-400 italic">Unassigned</span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-slate-500">Description</dt>
                                    <dd class="mt-1 text-sm text-slate-900">{{ $asset->description ?: 'No description provided.' }}</dd>
                                </div>
                            </dl>

                            {{-- Images --}}
                            @if($asset->images->count() > 0)
                                <div class="mt-6">
                                    <h4 class="text-sm font-medium text-slate-500 mb-2">Images</h4>
                                    <div class="flex gap-2 overflow-x-auto pb-2">
                                        @foreach($asset->images as $image)
                                            <a href="{{ $image->image }}" target="_blank" class="block shrink-0 h-24 w-24 rounded-lg overflow-hidden border border-slate-200 hover:opacity-90">
                                                <img src="{{ $image->image }}" alt="Asset Image" class="h-full w-full object-cover">
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        @elseif($activeTab === 'history')
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    @forelse($this->history as $log)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white 
                                                            {{ $log->action_type === 'restock' ? 'bg-green-500' : ($log->action_type === 'checkout' ? 'bg-blue-500' : ($log->action_type === 'checkin' ? 'bg-orange-500' : 'bg-slate-400')) }}">
                                                            @if($log->action_type === 'restock') <x-heroicon-o-plus class="h-5 w-5 text-white" />
                                                            @elseif($log->action_type === 'checkout') <x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5 text-white" />
                                                            @elseif($log->action_type === 'checkin') <x-heroicon-o-arrow-left-on-rectangle class="h-5 w-5 text-white" />
                                                            @else <x-heroicon-o-document class="h-5 w-5 text-white" />
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                        <div>
                                                            <p class="text-sm text-slate-500">
                                                                <span class="font-medium text-slate-900 capitalize">{{ $log->action_type }}</span> 
                                                                @if($log->units > 1)
                                                                    <span class="text-slate-600">({{ $log->units }} units)</span>
                                                                @endif
                                                                by <span class="text-slate-900">{{ $log->performedBy->name ?? 'Unknown' }}</span>
                                                            </p>
                                                            @if($log->targetUser)
                                                                <p class="text-xs text-slate-500 mt-0.5">Assigned to: {{ $log->targetUser->name }}</p>
                                                            @endif
                                                            @if($log->note)
                                                                <p class="text-xs text-slate-500 mt-0.5 italic">"{{ $log->note }}"</p>
                                                            @endif
                                                        </div>
                                                        <div class="whitespace-nowrap text-right text-sm text-slate-500">
                                                            <time datetime="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</time>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="py-4 text-center text-sm text-slate-500">No history available for this asset.</li>
                                    @endforelse
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
