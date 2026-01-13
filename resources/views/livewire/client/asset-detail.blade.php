<div class="min-h-screen bg-slate-50">
    {{-- Breadcrumbs --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm">
                    <li><a href="{{ route('app.dashboard') }}" class="text-slate-500 hover:text-slate-700">Dashboard</a></li>
                    <li><x-heroicon-o-chevron-right class="h-4 w-4 text-slate-400" /></li>
                    <li><a href="{{ route('app.facilities.show', $asset->facility) }}" class="text-slate-500 hover:text-slate-700">{{ $asset->facility->name }}</a></li>
                    @if($asset->store)
                        <li><x-heroicon-o-chevron-right class="h-4 w-4 text-slate-400" /></li>
                        <li><a href="{{ route('app.store.detail', [$asset->facility, $asset->store, 'activeTab' => 'assets']) }}" class="text-slate-500 hover:text-slate-700">{{ $asset->store->name }}</a></li>
                    @endif
                    <li><x-heroicon-o-chevron-right class="h-4 w-4 text-slate-400" /></li>
                    <li class="text-slate-900 font-medium">{{ $asset->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Asset Header --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ $asset->name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Serial: <span class="font-mono">{{ $asset->serial }}</span> • 
                        <span class="capitalize">{{ $asset->type }}</span>
                    </p>
                </div>
                <div class="flex gap-2">
                    <span class="px-3 py-1 text-sm rounded-full {{ $this->availableUnits > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $this->availableUnits }} Available
                    </span>
                    @if($asset->assignments->count() > 0)
                        <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                            {{ $asset->assignments->sum('quantity') }} Checked Out
                        </span>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            @if(!$actionType)
                <div class="flex gap-2 mt-6">
                    <button wire:click="setAction('restock')" class="flex-1 bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 flex justify-center items-center gap-2">
                        <x-heroicon-o-plus class="h-5 w-5" /> Restock
                    </button>
                    
                    @if($this->availableUnits > 0)
                        <button wire:click="setAction('checkout')" class="flex-1 bg-teal-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-teal-700 flex justify-center items-center gap-2">
                            <x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5" /> 
                            {{ $asset->type === 'consumable' ? 'Dispense' : 'Check Out' }}
                        </button>
                    @else
                        <button disabled class="flex-1 bg-slate-100 text-slate-400 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed flex justify-center items-center gap-2">
                            <x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5" /> Fully Checked Out
                        </button>
                    @endif

                    @if($asset->type !== 'consumable' && $asset->assignments->count() > 0)
                        <button wire:click="setAction('checkin')" class="flex-1 bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-50 flex justify-center items-center gap-2">
                            <x-heroicon-o-arrow-left-on-rectangle class="h-5 w-5" /> Check In
                        </button>
                    @endif
                </div>
            @endif

            {{-- Action Forms --}}
            @if($actionType)
                <div class="mt-6 bg-slate-50 rounded-lg p-6 border border-slate-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-slate-900 capitalize">{{ $actionType }} Asset</h3>
                        <button wire:click="setAction(null)" class="text-sm text-slate-500 hover:text-slate-700">Cancel</button>
                    </div>

                    <div class="space-y-4">
                        {{-- Quantity --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Quantity</label>
                            <input wire:model="quantity" type="number" min="1" max="{{ $actionType === 'checkout' ? $this->availableUnits : 999 }}" 
                                class="p-2 border bg-white mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            @error('quantity') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        {{-- Restock specific --}}
                        @if($actionType === 'restock')
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Cost per Unit (Optional)</label>
                                <div class="relative mt-1">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">&#8358;</span>
                                    <input wire:model="cost" type="number" step="0.01" class="p-2 border bg-white block w-full pl-10 rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" placeholder="0.00">
                                </div>
                            </div>
                        @endif

                        {{-- Checkout specific --}}
                        @if($actionType === 'checkout')
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Assign To</label>
                                <x-forms.searchable-select wire:model="targetUserId" :options="$this->userOptions" placeholder="Select User..." />
                                @error('targetUserId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            @if(in_array($asset->type, ['fixed', 'consumable']))
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Location / Space</label>
                                    <x-forms.searchable-select wire:model="targetSpaceId" :options="$this->spaceOptions" placeholder="Select Space..." />
                                    @error('targetSpaceId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        @endif

                        {{-- Checkin specific --}}
                        @if($actionType === 'checkin')
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Select Assignment to Return</label>
                                <select wire:model.live="selectedAssignmentId" class="p-2 bg-white border mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                    <option value="">Choose assignment...</option>
                                    @foreach($asset->assignments as $assignment)
                                        <option value="{{ $assignment->id }}">
                                            {{ $assignment->user->name }} - {{ $assignment->quantity }} units (since {{ $assignment->checked_out_at->format('M d, Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedAssignmentId') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            @if($selectedAssignmentId)
                                @php
                                    $selectedAssignment = $asset->assignments->firstWhere('id', $selectedAssignmentId);
                                @endphp
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Quantity Returning</label>
                                    <input wire:model="quantity" type="number" min="1" max="{{ $selectedAssignment->quantity }}" 
                                        class="p-2 border bg-white mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                    <p class="mt-1 text-xs text-slate-500">Maximum: {{ $selectedAssignment->quantity }} units</p>
                                    @error('quantity') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-medium text-slate-700">Condition</label>
                                <select wire:model="condition" class="border p-2 bg-white mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                    <option value="Good">Good</option>
                                    <option value="Fair">Fair</option>
                                    <option value="Damaged">Damaged</option>
                                    <option value="Needs Repair">Needs Repair</option>
                                </select>
                            </div>
                        @endif

                        {{-- Notes --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Notes (Optional)</label>
                            <textarea wire:model="notes" rows="2" class="p-2 border bg-white mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"></textarea>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button wire:click="submitAction" class="bg-teal-600 text-white px-6 py-2 rounded-md text-sm font-medium hover:bg-teal-700">
                                Confirm {{ ucfirst($actionType) }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Tabs --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-200">
            <div class="border-b border-slate-200">
                <nav class="-mb-px flex px-6 space-x-8">
                    <button wire:click="setTab('details')" class="{{ $activeTab === 'details' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                        Details
                    </button>
                    <button wire:click="setTab('history')" class="{{ $activeTab === 'history' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                        History
                    </button>
                    @if($asset->type !== 'consumable')
                        <button wire:click="setTab('assignments')" class="{{ $activeTab === 'assignments' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                            Assignments ({{ $asset->assignments->count() }})
                        </button>
                    @endif
                </nav>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Details Tab --}}
                @if($activeTab === 'details')
                    <dl class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Asset Name</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $asset->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Serial Number</dt>
                            <dd class="mt-1 text-sm text-slate-900 font-mono">{{ $asset->serial }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Type</dt>
                            <dd class="mt-1 text-sm text-slate-900 capitalize">{{ $asset->type }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Total Units</dt>
                            <dd class="mt-1 text-sm font-medium {{ $asset->units <= $asset->minimum ? 'text-red-600' : 'text-slate-900' }}">
                                {{ $asset->units }} {{ $asset->units === 1 ? 'unit' : 'units' }}
                            </dd>
                        </div>
                        @if($asset->space)
                            <div>
                                <dt class="text-sm font-medium text-slate-500">Location</dt>
                                <dd class="mt-1 text-sm text-slate-900">{{ $asset->space->name }}</dd>
                            </div>
                        @endif
                        @if($asset->supplierContact)
                            <div>
                                <dt class="text-sm font-medium text-slate-500">Supplier</dt>
                                <dd class="mt-1 text-sm text-slate-900">{{ $asset->supplierContact->first_name }} {{ $asset->supplierContact->last_name }}</dd>
                            </div>
                        @endif
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-slate-500">Description</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $asset->description ?: 'No description provided.' }}</dd>
                        </div>
                    </dl>

                    @if($asset->images->count() > 0)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-slate-700 mb-3">Images</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                @foreach($asset->images as $image)
                                    <a href="{{ $image->image }}" target="_blank" class="block aspect-square rounded-lg overflow-hidden border border-slate-200 hover:opacity-90">
                                        <img src="{{ $image->image }}" alt="Asset Image" class="w-full h-full object-cover">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                {{-- History Tab --}}
                @if($activeTab === 'history')
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @forelse($this->history as $log)
                                <li>
                                    <div class="relative pb-8">
                                        @if(!$loop->last)
                                            <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-slate-200"></span>
                                        @endif
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <span class="h-10 w-10 rounded-full flex items-center justify-center ring-8 ring-white 
                                                    {{ $log->action_type === 'restock' ? 'bg-green-500' : ($log->action_type === 'checkout' ? 'bg-blue-500' : 'bg-orange-500') }}">
                                                    @if($log->action_type === 'restock') <x-heroicon-o-plus class="h-5 w-5 text-white" />
                                                    @elseif($log->action_type === 'checkout') <x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5 text-white" />
                                                    @else <x-heroicon-o-arrow-left-on-rectangle class="h-5 w-5 text-white" />
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <p class="text-sm text-slate-900">
                                                        <span class="font-semibold capitalize">{{ $log->action_type }}</span> 
                                                        <span class="text-slate-600">{{ $log->quantity }} {{ $log->quantity === 1 ? 'unit' : 'units' }}</span>
                                                    </p>
                                                    <p class="mt-0.5 text-xs text-slate-500">
                                                        by {{ $log->performedBy->name ?? 'Unknown' }}
                                                        @if($log->targetUser) → {{ $log->targetUser->name }} @endif
                                                        @if($log->space) @ {{ $log->space->name }} @endif
                                                    </p>
                                                    @if($log->note)
                                                        <p class="mt-1 text-xs text-slate-600 italic">"{{ $log->note }}"</p>
                                                    @endif
                                                </div>
                                                <div class="mt-2 text-xs text-slate-500">
                                                    {{ $log->created_at->format('M d, Y g:i A') }} ({{ $log->created_at->diffForHumans() }})
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="py-8 text-center text-sm text-slate-500">No history available for this asset.</li>
                            @endforelse
                        </ul>
                    </div>
                @endif

                {{-- Assignments Tab --}}
                @if($activeTab === 'assignments')
                    <div class="space-y-4">
                        @forelse($asset->assignments as $assignment)
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">{{ $assignment->user->name }}</p>
                                    <p class="text-xs text-slate-500">
                                        {{ $assignment->quantity }} {{ $assignment->quantity === 1 ? 'unit' : 'units' }} • 
                                        Checked out {{ $assignment->checked_out_at->diffForHumans() }}
                                        @if($assignment->space) • {{ $assignment->space->name }} @endif
                                    </p>
                                    @if($assignment->notes)
                                        <p class="mt-1 text-xs text-slate-600 italic">"{{ $assignment->notes }}"</p>
                                    @endif
                                </div>
                                <button wire:click="setAction('checkin'); $set('selectedAssignmentId', {{ $assignment->id }})" 
                                    class="px-3 py-1.5 text-xs font-medium text-teal-700 bg-teal-50 rounded-md hover:bg-teal-100">
                                    Check In
                                </button>
                            </div>
                        @empty
                            <p class="py-8 text-center text-sm text-slate-500">No active assignments for this asset.</p>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
