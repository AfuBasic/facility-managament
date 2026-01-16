<div>
    <x-ui.page-header 
        title="SLA Policies" 
        description="Configure service level agreements for work order response and resolution times."
    >
        @can('create sla policy')
        <x-slot:actions>
            <x-ui.button wire:click="create">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create SLA Policy
            </x-ui.button>
        </x-slot:actions>
        @endcan
    </x-ui.page-header>

    <!-- Search Bar -->
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
                placeholder="Search SLA policies..."
            >
        </div>
    </div>

    <!-- Policies Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($policies as $policy)
        <x-ui.card class="{{ $policy->is_default ? 'ring-2 ring-teal-500' : '' }}">
            <div class="flex items-start justify-between mb-4">
                <div class="h-10 w-10 rounded-lg {{ $policy->is_active ? 'bg-teal-50 text-teal-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex items-center gap-2">
                    @if($policy->is_default)
                    <x-ui.badge variant="success">Default</x-ui.badge>
                    @endif
                    @if(!$policy->is_active)
                    <x-ui.badge variant="neutral">Inactive</x-ui.badge>
                    @endif
                </div>
            </div>
            
            <h3 class="text-lg font-semibold text-slate-900 mb-1">
                {{ $policy->name }}
            </h3>
            
            @if($policy->description)
            <p class="text-sm text-slate-500 mb-4">{{ Str::limit($policy->description, 80) }}</p>
            @endif

            <!-- SLA Times Table -->
            <div class="bg-slate-50 rounded-lg p-3 mb-4">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-slate-500">
                            <th class="text-left font-medium pb-2">Priority</th>
                            <th class="text-center font-medium pb-2">Response</th>
                            <th class="text-center font-medium pb-2">Resolution</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-700">
                        @foreach($policy->rules->sortBy(fn($r) => ['critical' => 1, 'high' => 2, 'medium' => 3, 'low' => 4][$r->priority] ?? 5) as $rule)
                        <tr>
                            <td class="py-1 font-medium capitalize">{{ $rule->priority }}</td>
                            <td class="py-1 text-center">{{ $rule->response_time_human }}</td>
                            <td class="py-1 text-center">{{ $rule->resolution_time_human }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <span class="text-xs text-slate-400">{{ $policy->work_orders_count }} work orders</span>
                <div class="flex items-center gap-1">
                    @can('edit sla policy')
                    @if(!$policy->is_default)
                    <x-ui.button size="sm" variant="ghost" wire:click="toggleDefault({{ $policy->id }})" title="Set as default">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                        </svg>
                    </x-ui.button>
                    @endif
                    <x-ui.button size="sm" variant="ghost" wire:click="edit({{ $policy->id }})">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </x-ui.button>
                    @endcan
                    @can('delete sla policy')
                    <x-ui.button 
                        size="sm"
                        variant="ghost-danger" 
                        :disabled="$policy->work_orders_count > 0"
                        @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                            detail: {
                                title: 'Delete SLA Policy',
                                message: 'Are you sure you want to delete this SLA policy? This cannot be undone.',
                                confirmText: 'Delete Policy',
                                cancelText: 'Cancel',
                                variant: 'danger',
                                action: () => $wire.delete({{ $policy->id }})
                            }
                        }))"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </x-ui.button>
                    @endcan
                </div>
            </div>
        </x-ui.card>
        @endforeach

        <!-- Empty State -->
        @if($policies->count() === 0)
        <div class="col-span-1 md:col-span-2 lg:col-span-3">
            <x-ui.empty-state 
                title="No SLA policies found" 
                description="Create your first SLA policy to define response and resolution time requirements."
            >
                <x-slot:icon>
                    <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.empty-state>
        </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($policies->hasPages())
    <div class="mt-6">
        {{ $policies->links() }}
    </div>
    @endif

    <!-- Modal -->
    <x-ui.modal show="showModal" title="{{ $isEditing ? 'Edit SLA Policy' : 'Create SLA Policy' }}" size="xl">
        <div class="space-y-6">
            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Policy Name</label>
                <input type="text" wire:model="name" class="w-full transition-colors rounded-lg border border-slate-200 p-2 focus:border-teal-500 focus:ring-teal-500" placeholder="e.g. Standard SLA">
                @error('name') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description (Optional)</label>
                <textarea wire:model="description" rows="2" class="w-full transition-colors rounded-lg border border-slate-200 p-2 focus:border-teal-500 focus:ring-teal-500" placeholder="Brief description of this policy..."></textarea>
            </div>

            <!-- Settings -->
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="isDefault" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    <span class="text-sm text-slate-700">Set as default policy</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="businessHoursOnly" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    <span class="text-sm text-slate-700">Business hours only</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="isActive" class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                    <span class="text-sm text-slate-700">Active</span>
                </label>
            </div>

            <!-- SLA Rules -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-3">Response & Resolution Times (in minutes)</label>
                <div class="bg-slate-50 rounded-lg p-4">
                    <table class="w-full">
                        <thead>
                            <tr class="text-sm text-slate-500">
                                <th class="text-left font-medium pb-3">Priority</th>
                                <th class="text-center font-medium pb-3">Response (mins)</th>
                                <th class="text-center font-medium pb-3">Resolution (mins)</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-700">
                            @foreach(['critical', 'high', 'medium', 'low'] as $priority)
                            <tr>
                                <td class="py-2 font-medium capitalize flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $priority === 'critical' ? 'bg-red-500' : ($priority === 'high' ? 'bg-orange-500' : ($priority === 'medium' ? 'bg-yellow-500' : 'bg-blue-500')) }}"></span>
                                    {{ $priority }}
                                </td>
                                <td class="py-2 px-2">
                                    <div class="relative">
                                        <input type="number" wire:model="rules.{{ $priority }}.response" min="1" class="w-full text-center rounded-lg border border-slate-200 p-2 pr-12 focus:border-teal-500 focus:ring-teal-500 text-sm">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">mins</span>
                                    </div>
                                    @error("rules.{$priority}.response") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </td>
                                <td class="py-2 px-2">
                                    <div class="relative">
                                        <input type="number" wire:model="rules.{{ $priority }}.resolution" min="1" class="w-full text-center rounded-lg border border-slate-200 p-2 pr-12 focus:border-teal-500 focus:ring-teal-500 text-sm">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">mins</span>
                                    </div>
                                    @error("rules.{$priority}.resolution") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="text-xs text-slate-400 mt-3">
                        <strong>Quick Reference:</strong> 60 = 1 hour, 240 = 4 hours, 480 = 8 hours, 1440 = 24 hours, 10080 = 1 week
                    </p>
                </div>
            </div>
        </div>
        
        <x-slot:footer>
            <x-ui.button variant="secondary" @click="show = false">Cancel</x-ui.button>
            <x-ui.button wire:click="save">{{ $isEditing ? 'Update Policy' : 'Create Policy' }}</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>
