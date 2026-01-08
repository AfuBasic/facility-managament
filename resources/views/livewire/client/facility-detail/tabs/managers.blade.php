{{-- Active Managers Section --}}
<div class="space-y-6">
    {{-- Header with Search and Add Button --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex-1 w-full sm:max-w-md">
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="managerSearch" 
                    class="block w-full border rounded-lg border-slate-300 pl-10 pr-3 py-2.5 text-slate-900 placeholder:text-slate-400 focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-colors"
                    placeholder="Search active managers..."
                >
            </div>
        </div>
        @can('create users')
            <button wire:click="openManagerModal" class="inline-flex items-center gap-x-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Manager
            </button>
        @endcan
    </div>

    {{-- Active Managers Table --}}
    @if($this->filteredActiveManagers->count() > 0)
        <div class="overflow-x-auto rounded-lg border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Manager</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Designation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Assigned Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($this->filteredActiveManagers as $manager)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-teal-100 rounded-full flex items-center justify-center">
                                        <span class="text-teal-700 font-semibold text-sm">{{ strtoupper(substr($manager->name ?? 'U', 0, 2)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-slate-900">{{ $manager->name ?? 'New User' }}</div>
                                        <div class="text-sm text-slate-500">{{ $manager->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 border border-blue-200">
                                    {{ $manager->pivot->designation }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ \Carbon\Carbon::parse($manager->pivot->assigned_at)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @can('edit users')
                                        <button wire:click="editManager({{ $manager->id }})" class="inline-flex items-center justify-center rounded-lg bg-slate-100 p-2 text-slate-700 hover:bg-slate-200 transition-all">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                    @endcan
                                    @can('delete users')
                                        <button 
                                            @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                                detail: {
                                                    title: 'Unassign Manager',
                                                    message: 'Are you sure you want to unassign this manager? They will be moved to dormant managers.',
                                                    confirmText: 'Unassign',
                                                    cancelText: 'Cancel',
                                                    variant: 'warning',
                                                    action: () => $wire.unassignManager({{ $manager->id }})
                                                }
                                            }))"
                                            class="inline-flex items-center justify-center rounded-lg bg-yellow-50 p-2 text-yellow-600 hover:bg-yellow-100 transition-all"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12 bg-slate-50 rounded-lg border border-slate-200">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">
                @if($managerSearch)
                    No managers found
                @else
                    No active managers yet
                @endif
            </h3>
            <p class="text-sm text-slate-500">
                @if($managerSearch)
                    Try adjusting your search criteria
                @else
                    Assign users to this facility to get started
                @endif
            </p>
        </div>
    @endif
</div>

{{-- Dormant Managers Section --}}
<div class="mt-12 space-y-6">
    <div class="flex items-center gap-3">
        <h3 class="text-lg font-semibold text-slate-900">Dormant Managers</h3>
        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
            {{ $this->filteredDormantManagers->count() }}
        </span>
    </div>

    {{-- Dormant Search --}}
    <div class="max-w-md">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="dormantManagerSearch" 
                class="block w-full rounded-lg border-slate-300 pl-10 pr-3 py-2.5 text-slate-900 placeholder:text-slate-400 focus:border-teal-500 border focus:ring-teal-500 sm:text-sm transition-colors"
                placeholder="Search dormant managers..."
            >
        </div>
    </div>

    {{-- Dormant Managers Table --}}
    @if($this->filteredDormantManagers->count() > 0)
        <div class="overflow-x-auto rounded-lg border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Manager</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Designation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Removed Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @foreach($this->filteredDormantManagers as $manager)
                        <tr class="hover:bg-slate-50 transition-colors opacity-60">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-slate-100 rounded-full flex items-center justify-center">
                                        <span class="text-slate-600 font-semibold text-sm">{{ strtoupper(substr($manager->name ?? 'U', 0, 2)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-slate-700">{{ $manager->name ?? 'New User' }}</div>
                                        <div class="text-sm text-slate-500">{{ $manager->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 border border-slate-200">
                                    {{ $manager->pivot->designation }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ \Carbon\Carbon::parse($manager->pivot->removed_at)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @can('edit users')
                                        <button 
                                            wire:click="reactivateManager({{ $manager->id }})"
                                            class="inline-flex items-center justify-center rounded-lg bg-green-50 p-2 text-green-600 hover:bg-green-100 transition-all"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                            </svg>
                                        </button>
                                    @endcan
                                    @can('delete users')
                                        <button 
                                            @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                                detail: {
                                                    title: 'Delete Manager Permanently',
                                                    message: 'Are you sure you want to permanently delete this manager? This action cannot be undone.',
                                                    confirmText: 'Delete Permanently',
                                                    cancelText: 'Cancel',
                                                    variant: 'danger',
                                                    action: () => $wire.deleteManager({{ $manager->id }})
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
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 bg-slate-50 rounded-lg border border-slate-200">
            <p class="text-sm text-slate-500">
                @if($dormantManagerSearch)
                    No dormant managers found
                @else
                    No dormant managers
                @endif
            </p>
        </div>
    @endif
</div>

{{-- Add/Edit Manager Modal --}}
@if($showManagerModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity" wire:click="closeManagerModal"></div>

            <div class="relative inline-block align-bottom bg-white rounded-2xl border border-slate-200 px-6 pt-5 pb-6 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-8">
                <div class="space-y-6">
                    {{-- Header --}}
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-slate-900">
                            {{ $isEditingManager ? 'Edit Manager Designation' : 'Assign Manager' }}
                        </h3>
                        <button wire:click="closeManagerModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form wire:submit="saveManager" class="space-y-5">
                        {{-- User Selection --}}
                        @if(!$isEditingManager)
                            <div>
                                <label for="selectedUserId" class="block text-sm font-medium text-slate-700 mb-2">
                                    Select User <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select 
                                        wire:model="selectedUserId" 
                                        id="selectedUserId"
                                        class="w-full appearance-none border rounded-lg border-slate-300 bg-white px-4 py-2.5 pr-10 text-sm text-slate-900 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all cursor-pointer hover:border-slate-400"
                                    >
                                        <option value="" class="text-slate-400">Select a user...</option>
                                        @foreach($this->availableUsers as $membership)
                                            <option value="{{ $membership->user->id }}" class="text-slate-900">
                                                {{ $membership->user->name ?? 'New User' }} • {{ $membership->user->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </div>
                                </div>
                                @error('selectedUserId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                
                                <p class="mt-2 text-sm text-slate-500">
                                    Can't find a user? 
                                    <a href="{{ route('app.users', ['invite' => 'true']) }}" class="font-medium text-teal-600 hover:text-teal-700 transition-colors">
                                        Invite user
                                    </a>
                                </p>
                            </div>
                        @endif

                        {{-- Designation --}}
                        <div>
                            <label for="managerDesignation" class="block text-sm font-medium text-slate-700 mb-2">
                                Designation <span class="text-red-500">*</span>
                            </label>
                            <input 
                                wire:model="managerDesignation" 
                                type="text" 
                                id="managerDesignation"
                                class="w-full border rounded-lg border-slate-300 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-teal-500 focus:ring-teal-500 transition-all"
                                placeholder="e.g., Facility Manager, Supervisor"
                            />
                            @error('managerDesignation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-3 pt-4">
                            <button 
                                type="submit"
                                class="flex-1 inline-flex justify-center items-center gap-2 rounded-lg bg-gradient-to-r from-teal-600 to-teal-500 px-4 py-2.5 text-sm font-semibold text-white hover:from-teal-700 hover:to-teal-600 transition-all"
                            >
                                {{ $isEditingManager ? 'Update Designation' : 'Assign Manager' }}
                            </button>
                            <button 
                                type="button"
                                wire:click="closeManagerModal"
                                class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors"
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
