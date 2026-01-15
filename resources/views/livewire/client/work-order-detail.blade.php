<div>
    <x-ui.page-header
        :title="'Work Order ' . $workOrder->workorder_serial"
        :description="$workOrder->title"
    >
        <x-slot:actions>
            <div class="flex gap-3">
                @can('update', $workOrder)
                    <a href="{{ route('app.work-orders.edit', $workOrder) }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors border border-transparent text-white bg-teal-600 hover:bg-teal-700 focus:ring-teal-500">
                        <x-heroicon-o-pencil class="h-5 w-5 mr-2" />
                        Edit
                    </a>
                @endcan
                @can('delete', $workOrder)
                    <button wire:click="delete" wire:confirm="Are you sure you want to delete this work order? This action cannot be undone." class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors border border-transparent text-white bg-red-600 hover:bg-red-700 focus:ring-red-500">
                        <x-heroicon-o-trash class="h-5 w-5 mr-2" />
                        Delete
                    </button>
                @endcan
                <a href="{{ route('app.work-orders.index') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 focus:ring-teal-500">
                    <x-heroicon-o-arrow-left class="h-5 w-5 mr-2" />
                    Back to List
                </a>
            </div>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Tab Navigation --}}
            <div class="border-b border-slate-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button wire:click="setTab('details')"
                        class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors {{ $activeTab === 'details' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                        Details
                    </button>
                    <button wire:click="setTab('history')"
                        class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors {{ $activeTab === 'history' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                        History
                        @if($this->stateChanges->count() > 0)
                        <span class="ml-2 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $this->stateChanges->count() }}</span>
                        @endif
                    </button>
                    @if($this->updates->count() > 0)
                    <button wire:click="setTab('updates')"
                        class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors {{ $activeTab === 'updates' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                        Updates
                        <span class="ml-2 rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-600">{{ $this->updates->count() }}</span>
                    </button>
                    @endif
                    @if($this->assignmentHistory->count() > 0)
                    <button wire:click="setTab('assignments')"
                        class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium transition-colors {{ $activeTab === 'assignments' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}">
                        Assignments
                        <span class="ml-2 rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-600">{{ $this->assignmentHistory->count() }}</span>
                    </button>
                    @endif
                </nav>
            </div>

            {{-- Details Tab --}}
            @if($activeTab === 'details')
            {{-- Work Order Details --}}
            <x-ui.card>
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Details</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-500">Description</label>
                        <p class="mt-1 text-sm text-slate-900">{{ $workOrder->description }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-500">Facility</label>
                            <p class="mt-1 text-sm text-slate-900">{{ $workOrder->facility->name }}</p>
                        </div>

                        @if($workOrder->asset)
                        <div>
                            <label class="block text-sm font-medium text-slate-500">Related Asset</label>
                            <p class="mt-1 text-sm text-slate-900">
                                <a href="{{ route('app.asset.detail', $workOrder->asset) }}" class="text-teal-600 hover:text-teal-700">
                                    {{ $workOrder->asset->name }}
                                </a>
                            </p>
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-500">Priority</label>
                            <div class="mt-1">
                                @php
                                    $priorityVariants = [
                                        'low' => 'neutral',
                                        'medium' => 'info',
                                        'high' => 'warning',
                                        'critical' => 'danger',
                                    ];
                                @endphp
                                <x-ui.badge :variant="$priorityVariants[$workOrder->priority]">
                                    {{ ucfirst($workOrder->priority) }}
                                </x-ui.badge>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-500">Status</label>
                            <div class="mt-1">
                                @php
                                    $statusVariants = [
                                        'reported' => 'primary',
                                        'approved' => 'success',
                                        'assigned' => 'info',
                                        'in_progress' => 'warning',
                                        'on_hold' => 'danger',
                                        'completed' => 'success',
                                        'closed' => 'neutral',
                                    ];
                                @endphp
                                <x-ui.badge :variant="$statusVariants[$workOrder->status] ?? 'neutral'">
                                    {{ ucfirst(str_replace('_', ' ', $workOrder->status)) }}
                                </x-ui.badge>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-500">Reported By</label>
                        <p class="mt-1 text-sm text-slate-900">
                            {{ $workOrder->reportedBy->name }}
                            <span class="text-slate-500">on {{ $workOrder->reported_at->format('M d, Y g:i A') }}</span>
                        </p>
                    </div>

                    @if($workOrder->assignedTo)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-500">Assigned To</label>
                            <p class="mt-1 text-sm text-slate-900">
                                {{ $workOrder->assignedTo->name }}
                                @if($workOrder->assigned_at)
                                    <span class="text-slate-500">on {{ $workOrder->assigned_at->format('M d, Y g:i A') }}</span>
                                @endif
                            </p>
                        </div>

                        @if($workOrder->allocatedAssets && $workOrder->allocatedAssets->count() > 0)
                        <div>
                            <label class="block text-sm font-medium text-slate-500">Reserved Assets</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @foreach($workOrder->allocatedAssets as $workOrderAsset)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        {{ $workOrderAsset->asset->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($workOrder->completion_notes)
                    <div>
                        <label class="block text-sm font-medium text-slate-500">Completion Notes</label>
                        <p class="mt-1 text-sm text-slate-900">{{ $workOrder->completion_notes }}</p>
                    </div>
                    @endif
                </div>
            </x-ui.card>
            @endif

            {{-- History Tab --}}
            @if($activeTab === 'history')
            <x-ui.card>
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Status History</h3>
                </div>
                <div class="p-6">
                    @if($this->stateChanges->isNotEmpty())
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($this->stateChanges as $log)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-teal-500 flex items-center justify-center ring-8 ring-white">
                                                <x-heroicon-o-arrow-path class="h-5 w-5 text-white" />
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm text-slate-900">
                                                    @php
                                                        $message = match($log->new_state) {
                                                            'approved' => 'Work order was approved',
                                                            'rejected' => 'Work order was rejected',
                                                            'assigned' => 'Assigned to ' . ($workOrder->assignedTo->name ?? 'user'),
                                                            'in_progress' => 'Work started',
                                                            'completed' => 'Work order was completed',
                                                            'closed' => 'Work order was closed',
                                                            default => 'Status changed to ' . ucfirst(str_replace('_', ' ', $log->new_state))
                                                        };
                                                    @endphp
                                                    {{ $message }}
                                                </p>
                                                @if($log->note)
                                                <p class="mt-1 text-sm text-slate-500">{{ $log->note }}</p>
                                                @endif
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-slate-500">
                                                <div>{{ $log->changedBy->name }}</div>
                                                <div>{{ $log->changed_at->format('M d, Y') }}</div>
                                                <div>{{ $log->changed_at->format('g:i A') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <p class="text-sm text-slate-500 text-center py-8">No status changes yet.</p>
                    @endif
                </div>
            </x-ui.card>
            @endif

            {{-- Updates Tab --}}
            @if($activeTab === 'updates')
            <x-ui.card>
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Progress Updates</h3>
                </div>
                <div class="p-6">
                    @if($this->updates->isNotEmpty())
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($this->updates as $update)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-blue-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <x-heroicon-o-chat-bubble-left-right class="h-5 w-5 text-white" />
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-blue-600">{{ $update->changedBy->name }}</p>
                                                @if($update->note)
                                                <p class="mt-1 text-sm text-slate-900">{{ $update->note }}</p>
                                                @endif
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-slate-500">
                                                <div>{{ $update->changed_at->format('M d, Y') }}</div>
                                                <div>{{ $update->changed_at->format('g:i A') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <p class="text-sm text-slate-500 text-center py-8">No updates yet.</p>
                    @endif
                </div>
            </x-ui.card>
            @endif

            {{-- Assignments Tab --}}
            @if($activeTab === 'assignments')
            <x-ui.card>
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Assignment History</h3>
                </div>
                <div class="p-6">
                    @if($this->assignmentHistory->isNotEmpty())
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($this->assignmentHistory as $assignment)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-purple-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full {{ $assignment->is_current ? 'bg-purple-500' : 'bg-slate-400' }} flex items-center justify-center ring-8 ring-white">
                                                <x-heroicon-o-user class="h-5 w-5 text-white" />
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm text-slate-900">
                                                    <span class="font-medium">{{ $assignment->assignee->name }}</span>
                                                    @if($assignment->is_current)
                                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">Current</span>
                                                    @endif
                                                </p>
                                                <p class="mt-1 text-sm text-slate-500">
                                                    Assigned by {{ $assignment->assigner->name }}
                                                </p>
                                                @if($assignment->assignment_note)
                                                <p class="mt-1 text-sm text-slate-500 italic">"{{ $assignment->assignment_note }}"</p>
                                                @endif
                                                @if($assignment->unassigned_at)
                                                <p class="mt-2 text-sm text-slate-500">
                                                    <span class="text-red-600">Unassigned</span> by {{ $assignment->unassigner?->name ?? 'System' }}
                                                    on {{ $assignment->unassigned_at->format('M d, Y g:i A') }}
                                                    @if($assignment->unassignment_reason)
                                                    <br><span class="italic">"{{ $assignment->unassignment_reason }}"</span>
                                                    @endif
                                                </p>
                                                @endif
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-slate-500">
                                                <div>{{ $assignment->assigned_at->format('M d, Y') }}</div>
                                                <div>{{ $assignment->assigned_at->format('g:i A') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <p class="text-sm text-slate-500 text-center py-8">No assignment history.</p>
                    @endif
                </div>
            </x-ui.card>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Actions Card --}}
            <x-ui.card>
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($workOrder->status === 'reported')
                        {{-- Approve/Reject --}}
                        @can('approve', $workOrder)
                        <x-ui.button @click="$wire.showApproveModal = true" class="w-full">
                            <x-heroicon-o-check-circle class="h-5 w-5 mr-2" />
                            Approve & Close
                        </x-ui.button>
                        @endcan
                        @can('reject', $workOrder)
                        <x-ui.button variant="danger" @click="$wire.showRejectModal = true" class="w-full">
                            <x-heroicon-o-x-circle class="h-5 w-5 mr-2" />
                            Reject
                        </x-ui.button>
                        @endcan
                    @elseif($workOrder->status === 'approved')
                        {{-- Assign --}}
                        @can('assign', $workOrder)
                        <x-ui.button @click="$wire.showAssignModal = true" class="w-full">
                            <x-heroicon-o-user-plus class="h-5 w-5 mr-2" />
                            Assign to User
                        </x-ui.button>
                        @endcan
                    @elseif($workOrder->status === 'assigned')
                        {{-- Start Work (only assignee) --}}
                        @can('start', $workOrder)
                        <x-ui.button @click="$wire.showStartModal = true" class="w-full">
                            <x-heroicon-o-play class="h-5 w-5 mr-2" />
                            Start Work
                        </x-ui.button>
                        @endcan
                    @elseif($workOrder->status === 'in_progress')
                        {{-- Add Update (creator or assignee) --}}
                        @can('addUpdate', $workOrder)
                        <x-ui.button variant="secondary" @click="$wire.showUpdateModal = true" class="w-full">
                            <x-heroicon-o-chat-bubble-left-right class="h-5 w-5 mr-2" />
                            Add Update
                        </x-ui.button>
                        @endcan

                        {{-- Pause Work --}}
                        @can('pause', $workOrder)
                        <x-ui.button variant="warning" wire:click="pause" wire:confirm="Are you sure you want to pause this work order?" class="w-full">
                            <x-heroicon-o-pause class="h-5 w-5 mr-2" />
                            Pause
                        </x-ui.button>
                        @endcan

                        {{-- Mark as Done (only assignee) --}}
                        @can('markDone', $workOrder)
                        <x-ui.button @click="$wire.showCompleteModal = true" class="w-full">
                            <x-heroicon-o-check-badge class="h-5 w-5 mr-2" />
                            Mark as Done
                        </x-ui.button>
                        @endcan

                        {{-- Reassign --}}
                        @can('reassign', $workOrder)
                        <x-ui.button variant="secondary" @click="$wire.showReassignModal = true" class="w-full">
                            <x-heroicon-o-arrow-path-rounded-square class="h-5 w-5 mr-2" />
                            Reassign
                        </x-ui.button>
                        @endcan

                    @elseif($workOrder->status === 'on_hold')
                        {{-- Resume Work --}}
                        <x-ui.button wire:click="resume" class="w-full">
                            <x-heroicon-o-play class="h-5 w-5 mr-2" />
                            Resume
                        </x-ui.button>
                    @elseif($workOrder->status === 'completed')
                        {{-- Approve Completion (only creator) - this will close the work order --}}
                        @can('approveCompletion', $workOrder)
                        <x-ui.button wire:click="approveCompletion" wire:confirm="Are you sure you want to approve and close this work order?" class="w-full">
                            <x-heroicon-o-check class="h-5 w-5 mr-2" />
                            Approve
                        </x-ui.button>
                        @endcan
                        {{-- Reject Completion (only creator) --}}
                        @can('rejectCompletion', $workOrder)
                        <x-ui.button variant="danger" @click="$wire.showRejectModal = true" class="w-full">
                            <x-heroicon-o-x-mark class="h-5 w-5 mr-2" />
                            Request Changes
                        </x-ui.button>
                        @endcan
                    @elseif($workOrder->status === 'closed')
                        <p class="text-sm text-slate-500 text-center py-4 mb-3">This work order is closed.</p>
                        {{-- Reopen (only creator) --}}
                        @can('reopen', $workOrder)
                        <x-ui.button variant="secondary" @click="$wire.showReopenModal = true" class="w-full">
                            <x-heroicon-o-arrow-uturn-left class="h-5 w-5 mr-2" />
                            Reopen Work Order
                        </x-ui.button>
                        @endcan
                    @elseif($workOrder->status === 'rejected')
                        <p class="text-sm text-slate-500 text-center py-4">This work order was rejected.</p>
                    @endif
                </div>
            </x-ui.card>

            {{-- Metadata Card --}}
            <x-ui.card>
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Metadata</h3>
                </div>
                <div class="p-6 space-y-3 text-sm">
                    <div>
                        <span class="text-slate-500">Created:</span>
                        <span class="text-slate-900">{{ $workOrder->created_at->format('M d, Y g:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500">Last Updated:</span>
                        <span class="text-slate-900">{{ $workOrder->updated_at->format('M d, Y g:i A') }}</span>
                    </div>
                    @if($workOrder->time_spent)
                    <div>
                        <span class="text-slate-500">Time Spent:</span>
                        <span class="text-slate-900">{{ $workOrder->time_spent }} minutes</span>
                    </div>
                    @endif
                    @if($workOrder->total_cost)
                    <div>
                        <span class="text-slate-500">Total Cost:</span>
                        <span class="text-slate-900">${{ number_format($workOrder->total_cost, 2) }}</span>
                    </div>
                    @endif
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- Approve Modal --}}
    <x-ui.modal show="showApproveModal" title="Approve Work Order">
        <div class="space-y-4">
            <p class="text-sm text-slate-600">Are you sure you want to approve this work order?</p>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Note (Optional)</label>
                <textarea wire:model="approval_note" rows="3"
                    class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Add any approval notes..."></textarea>
            </div>
        </div>

        <x-slot:footer>
            <x-ui.button variant="secondary" @click="$wire.showApproveModal = false">Cancel</x-ui.button>
            <x-ui.button wire:click="approve" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="approve">Approve</span>
                <span wire:loading wire:target="approve">Approving...</span>
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Reject Modal (handles both initial rejection and completion rejection) --}}
    <x-ui.modal show="showRejectModal" :title="$workOrder->status === 'completed' ? 'Request Changes' : 'Reject Work Order'">
        <div class="space-y-4">
            <p class="text-sm text-slate-600">
                @if($workOrder->status === 'completed')
                    Please explain what changes or additional work is needed.
                @else
                    Please provide a reason for rejecting this work order.
                @endif
            </p>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Reason *</label>
                <textarea wire:model="rejection_reason" rows="3"
                    class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="{{ $workOrder->status === 'completed' ? 'Describe what needs to be fixed or improved...' : 'Explain why this work order is being rejected...' }}"></textarea>
                @error('rejection_reason') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <x-slot:footer>
            <x-ui.button variant="secondary" @click="$wire.showRejectModal = false">Cancel</x-ui.button>
            @if($workOrder->status === 'completed')
                <x-ui.button variant="danger" wire:click="rejectCompletion" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="rejectCompletion">Request Changes</span>
                    <span wire:loading wire:target="rejectCompletion">Sending...</span>
                </x-ui.button>
            @else
                <x-ui.button variant="danger" wire:click="reject" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="reject">Reject</span>
                    <span wire:loading wire:target="reject">Rejecting...</span>
                </x-ui.button>
            @endif
        </x-slot:footer>
    </x-ui.modal>

    {{-- Assign Modal --}}
    <x-ui.modal show="showAssignModal" title="Assign Work Order">
        <div class="space-y-4">
            <div>
                <x-forms.searchable-select
                    wire:model="assigned_user_id"
                    :options="$this->users"
                    :selected="$assigned_user_id"
                    label="Assign To *"
                    placeholder="Select user..."
                />
                @error('assigned_user_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            @if($this->availableAssets->isNotEmpty())
            <div>
                <x-forms.multi-select
                    wire:model="selected_assets"
                    :options="$this->availableAssets"
                    :selected="$selected_assets"
                    label="Allocate Assets (Optional)"
                    placeholder="Select assets to allocate..."
                />
                <p class="mt-1 text-xs text-slate-500">These assets will be checked out to the assignee for this work order.</p>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Assignment Note (Optional)</label>
                <textarea wire:model="assignment_note" rows="3"
                    class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Add any instructions or notes for the assignee..."></textarea>
            </div>
        </div>

        <x-slot:footer>
            <x-ui.button variant="secondary" @click="$wire.showAssignModal = false">Cancel</x-ui.button>
            <x-ui.button wire:click="assign" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="assign">Assign</span>
                <span wire:loading wire:target="assign">Assigning...</span>
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Mark as Done Modal --}}
    <x-ui.modal show="showCompleteModal" title="Mark as Done">
        <div class="space-y-4">
            <p class="text-sm text-slate-600">Mark this work order as completed. The creator will need to approve or request changes before it can be closed.</p>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Completion Notes</label>
                <textarea wire:model="completion_notes" rows="4"
                    class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Describe what was done to complete this work order..."></textarea>
                @error('completion_notes') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Time Spent (minutes)</label>
                    <input type="number" wire:model="time_spent" min="0"
                        class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                        placeholder="0">
                    @error('time_spent') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Total Cost ($)</label>
                    <input type="number" wire:model="total_cost" min="0" step="0.01"
                        class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                        placeholder="0.00">
                    @error('total_cost') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <x-slot:footer>
            <x-ui.button variant="secondary" @click="$wire.showCompleteModal = false">Cancel</x-ui.button>
            <x-ui.button wire:click="markDone" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="markDone">Mark as Completed</span>
                <span wire:loading wire:target="markDone">Submitting...</span>
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Close Modal --}}
    <x-ui.modal show="showCloseModal" title="Close Work Order">
        <div class="space-y-4">
            <p class="text-sm text-slate-600">Close this work order to finalize it. This action marks the work order as complete and archived.</p>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Closure Note (Optional)</label>
                <textarea wire:model="closure_note" rows="3"
                    class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Add any final notes or comments..."></textarea>
            </div>
        </div>

        <x-slot:footer>
            <x-ui.button variant="secondary" @click="$wire.showCloseModal = false">Cancel</x-ui.button>
            <x-ui.button wire:click="close" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="close">Close Work Order</span>
                <span wire:loading wire:target="close">Closing...</span>
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Start Work Modal --}}
    <x-ui.modal show="showStartModal" title="Start Work">
        <p class="text-sm text-slate-600">
            Are you ready to start working on this work order? This will change the status to "In Progress".
        </p>

        <x-slot:footer>
            <x-ui.button variant="secondary" @click="$wire.showStartModal = false">Cancel</x-ui.button>
            <x-ui.button wire:click="start" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="start">Start Work</span>
                <span wire:loading wire:target="start">Starting...</span>
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Add Update Modal --}}
    <x-ui.modal show="showUpdateModal" title="Add Progress Update">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Update Note *</label>
                <textarea wire:model="update_note" rows="4"
                    class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Describe the progress made or any issues encountered..."></textarea>
                @error('update_note') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <x-slot:footer>
            <x-ui.button variant="secondary" @click="$wire.showUpdateModal = false">Cancel</x-ui.button>
            <x-ui.button wire:click="addUpdate" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="addUpdate">Add Update</span>
                <span wire:loading wire:target="addUpdate">Adding...</span>
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Reopen Modal --}}
    <x-ui.modal show="showReopenModal" title="Reopen Work Order">
        <div class="space-y-4">
            <p class="text-sm text-slate-600">
                Reopening this work order will change its status back to "In Progress". This allows you to continue working on it or add more updates.
            </p>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Reason for Reopening (Optional)</label>
                <textarea wire:model="reopen_reason" rows="3"
                    class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Explain why this work order needs to be reopened..."></textarea>
                @error('reopen_reason') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <x-slot:footer>
            <x-ui.button variant="secondary" @click="$wire.showReopenModal = false">Cancel</x-ui.button>
            <x-ui.button wire:click="reopen" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="reopen">Reopen Work Order</span>
                <span wire:loading wire:target="reopen">Reopening...</span>
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Reassign Modal --}}
    <x-ui.modal show="showReassignModal" title="Reassign Work Order">
        <div class="space-y-4">
            <p class="text-sm text-slate-600">
                Currently assigned to: <strong>{{ $workOrder->assignedTo?->name ?? 'Unassigned' }}</strong>
            </p>
            <div>
                <x-forms.searchable-select
                    wire:model="reassign_user_id"
                    :options="$this->reassignableUsers"
                    :selected="$reassign_user_id"
                    label="Reassign To *"
                    placeholder="Select new assignee..."
                />
                @error('reassign_user_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Reason for Reassignment (Optional)</label>
                <textarea wire:model="reassign_reason" rows="3"
                    class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Explain why this work order is being reassigned..."></textarea>
                @error('reassign_reason') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>
        </div>

        <x-slot:footer>
            <x-ui.button variant="secondary" @click="$wire.showReassignModal = false">Cancel</x-ui.button>
            <x-ui.button wire:click="reassign" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="reassign">Reassign</span>
                <span wire:loading wire:target="reassign">Reassigning...</span>
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>
