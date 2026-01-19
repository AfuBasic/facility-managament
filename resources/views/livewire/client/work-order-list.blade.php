<div>
    <x-ui.page-header 
        title="Work Orders" 
        description="Manage facility maintenance requests and track their progress."
    >
        <x-slot:actions>
            <button wire:click="openCreateModal" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors border border-transparent text-white bg-teal-600 hover:bg-teal-700 focus:ring-teal-500">
                <x-heroicon-o-plus class="h-5 w-5 mr-2" />
                New Work Order
            </button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Search --}}
        <div class="md:col-span-2">
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <x-heroicon-o-magnifying-glass class="h-5 w-5 text-slate-400" />
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    class="block border w-full bg-white rounded-lg border-slate-300 pl-10 pr-3 py-2.5 text-slate-900 placeholder:text-slate-400 focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-colors"
                    placeholder="Search work orders..."
                >
            </div>
        </div>

        {{-- Status Filter --}}
        <div>
            <x-forms.searchable-select
                wire:model.live="status"
                :options="[
                    '' => 'All Statuses',
                    'reported' => 'Reported',
                    'approved' => 'Approved',
                    'assigned' => 'Assigned',
                    'in_progress' => 'In Progress',
                    'on_hold' => 'On Hold',
                    'completed' => 'Completed',
                    'closed' => 'Closed'
                ]"
                :selected="$status"
                placeholder="All Statuses"
            />
        </div>

        {{-- Priority Filter --}}
        <div>
            <x-forms.searchable-select
                wire:model.live="priority"
                :options="[
                    '' => 'All Priorities',
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'critical' => 'Critical'
                ]"
                :selected="$priority"
                placeholder="All Priorities"
            />
        </div>
    </div>

    {{-- Work Orders Table --}}
    <x-ui.card class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Facility</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Priority</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Assigned To</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Created</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($workOrders as $workOrder)
                        <tr wire:key="work-order-{{ $workOrder->id }}" class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                {{ $workOrder->workorder_serial }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-900">{{ $workOrder->title }}</div>
                                <div class="text-sm text-slate-500 mt-1">{{ Str::limit($workOrder->description, 60) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $workOrder->facility->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $workOrder->assignedTo?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $workOrder->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('app.work-orders.show', $workOrder) }}" 
                                    class="text-teal-600 hover:text-teal-900 font-medium">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-500">
                                No work orders found. 
                                <button wire:click="openCreateModal" class="text-teal-600 hover:text-teal-700 font-medium">Create your first work order</button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    {{-- Pagination --}}
    @if($workOrders->hasPages())
    <div class="mt-6">
        {{ $workOrders->links() }}
    </div>
    @endif

    {{-- Create Work Order Modal --}}
    <x-ui.modal show="showCreateModal" title="Create Work Order" maxWidth="2xl">
        <form wire:submit="saveWorkOrder" class="space-y-6">
            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input wire:model="newTitle" type="text" 
                    class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Brief description of the issue">
                @error('newTitle') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description <span class="text-red-500">*</span></label>
                <textarea wire:model="newDescription" rows="4"
                    class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500"
                    placeholder="Detailed description of the problem"></textarea>
                @error('newDescription') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            {{-- Priority --}}
            <div>
                <x-forms.searchable-select
                    wire:model="newPriority"
                    :options="[
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'critical' => 'Critical'
                    ]"
                    :selected="$newPriority"
                    label="Priority *"
                    placeholder="Select priority..."
                />
                @error('newPriority') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            {{-- Facility --}}
            <div>
                <x-forms.searchable-select
                    wire:model.live="newFacilityId"
                    :options="$this->facilities"
                    :selected="$newFacilityId"
                    label="Facility *"
                    placeholder="Select facility..."
                />
                @error('newFacilityId') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
            </div>

            {{-- Space (Optional) --}}
            @if($newFacilityId && $this->spaces->isNotEmpty())
                <div>
                    <x-forms.searchable-select
                        wire:model="newSpaceId"
                        :options="$this->spaces"
                        :selected="$newSpaceId"
                        label="Space (Optional)"
                        placeholder="Select space..."
                    />
                    @error('newSpaceId') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                <button type="button" @click="show = false" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 focus:ring-teal-500">
                    Cancel
                </button>
                <x-ui.button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveWorkOrder">Submit Work Order</span>
                    <span wire:loading wire:target="saveWorkOrder">Submitting...</span>
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
