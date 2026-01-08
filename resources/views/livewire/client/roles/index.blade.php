<div>
    <x-ui.page-header 
    title="Roles & Permissions" 
    description="Manage access control for your organization members."
    >
    @can('create roles')
    <x-slot:actions>
    <x-ui.button wire:click="create">
        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Create Role
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
            placeholder="Search roles..."
        >
    </div>
</div>

<!-- Roles Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($roles as $role)
    <x-ui.card>
        <div class="flex items-start justify-between mb-4">
            <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                </svg>
            </div>
            <div class="flex items-center gap-2">
                @can('edit roles')
                <x-ui.button variant="ghost" wire:click="edit({{ $role->id }})">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </x-ui.button>
                @endcan
                @can('delete roles')
                <x-ui.button 
                variant="ghost-danger" 
                @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                    detail: {
                        title: 'Delete Role',
                        message: 'Are you sure you want to delete this role? Users with this role will lose their permissions.',
                        confirmText: 'Delete Role',
                        cancelText: 'Cancel',
                        variant: 'danger',
                        action: () => $wire.delete({{ $role->id }})
                    }
                }))"
                >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </x-ui.button>
            @endcan
        </div>
    </div>
    
    <h3 class="text-lg font-semibold text-slate-900 group-hover:text-teal-900 transition-colors mb-2">
        {{ ucwords($role->name) }}
    </h3>
    
    <div class="flex flex-wrap gap-1.5 mt-4 max-h-24 overflow-y-auto custom-scrollbar">
        @foreach($role->permissions->take(8) as $permission)
        <x-ui.badge>{{ ucwords($permission->name) }}</x-ui.badge>
        @endforeach
        @if($role->permissions->count() > 8)
        <x-ui.badge variant="neutral">+{{ $role->permissions->count() - 8 }} more</x-ui.badge>
        @endif
    </div>
</x-ui.card>
@endforeach

<!-- Empty State -->
@if($roles->count() === 0)
<div class="col-span-1 md:col-span-2 lg:col-span-3">
    <x-ui.empty-state 
    title="No roles found" 
    description="Create your first role to start assigning permissions."
    >
    <x-slot:icon>
    <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
    </svg>
</x-slot:icon>
</x-ui.empty-state>
</div>
@endif
</div>

<!-- Pagination -->
@if($roles->hasPages())
<div class="mt-6">
    {{ $roles->links() }}
</div>
@endif

<!-- Modal -->
<x-ui.modal show="showModal" title="{{ $isEditing ? 'Edit Role' : 'Create New Role' }}">
    <!-- Name -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-slate-700 mb-1">Role Name</label>
        <input type="text" wire:model="name" class="w-full transition-colors rounded-lg border border-slate-200 p-2 focus:border-teal-500 focus:ring-teal-500" placeholder="e.g. Maintenance Manager">
        @error('name') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
    </div>
    
    <!-- Permissions Grid -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <label class="block text-sm font-medium text-slate-700">Permissions</label>
            <button type="button" wire:click="toggleSelectAll" class="text-sm font-medium text-teal-600 hover:text-teal-700 hover:underline transition-colors focus:outline-none">
                {{ count($selectedPermissions) === \Spatie\Permission\Models\Permission::count() ? 'Deselect All' : 'Select All' }}
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($groupedPermissions as $group => $perms)
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 hover:border-teal-100 transition-colors">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2 mb-3">
                    <h4 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">{{ $group }}</h4>
                    <button type="button" wire:click="toggleGroup('{{ $group }}')" class="text-xs font-medium text-slate-400 hover:text-teal-600 transition-colors focus:outline-none">
                        Check All
                    </button>
                </div>
                
                <div class="space-y-3">
                    @foreach($perms as $perm)
                    <x-ui.checkbox 
                    wire:key="perm-{{ $perm->id }}"
                    wire:model="selectedPermissions" 
                    value="{{ $perm->name }}"
                    label="{{ str_replace($group . ' ', '', str_replace('view', 'View', str_replace('create', 'Create', str_replace('edit', 'Edit', str_replace('delete', 'Delete', ucwords($perm->name)))))) }}"
                    />
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <x-slot:footer>
    <x-ui.button variant="secondary" @click="show = false">Cancel</x-ui.button>
    <x-ui.button wire:click="save">Save Role</x-ui.button>
</x-slot:footer>
</x-ui.modal>
</div>
