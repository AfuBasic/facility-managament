<div>
    <x-ui.page-header 
        title="Roles & Permissions" 
        description="Manage access control for your organization members."
    >
        <x-slot:actions>
            <x-ui.button wire:click="create">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create Role
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>
    
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
                    <x-ui.button variant="ghost" wire:click="edit({{ $role->id }})">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </x-ui.button>
                    <x-ui.button variant="ghost-danger" wire:click="delete({{ $role->id }})" wire:confirm="Are you sure you want to delete this role?">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </x-ui.button>
                </div>
            </div>
            
            <h3 class="text-lg font-semibold text-slate-900 group-hover:text-teal-900 transition-colors mb-2">
                {{ ucwords($role->name) }}
            </h3>
            
            <div class="flex flex-wrap gap-1.5 mt-4 max-h-24 overflow-y-auto custom-scrollbar">
                @foreach($role->permissions->take(8) as $permission)
                <x-ui.badge>{{ $permission->name }}</x-ui.badge>
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
    
    <!-- Modal -->
    <x-ui.modal show="showModal" title="{{ $isEditing ? 'Edit Role' : 'Create New Role' }}">
        <!-- Name -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-1">Role Name</label>
            <input type="text" wire:model="name" class="w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500 transition-colors" placeholder="e.g. Maintenance Manager">
            @error('name') <span class="text-sm text-red-500 mt-1">{{ $message }}</span> @enderror
        </div>
        
        <!-- Permissions Grid -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-3">Permissions</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($groupedPermissions as $group => $perms)
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                    <h4 class="text-sm font-semibold text-slate-900 mb-3 uppercase tracking-wide border-b border-slate-200 pb-2">{{ $group }}</h4>
                    <div class="space-y-2">
                        @foreach($perms as $perm)
                        <div class="flex items-start">
                            <div class="flex h-5 items-center">
                                <input id="perm-{{ $perm->id }}" 
                                wire:model="selectedPermissions" 
                                value="{{ $perm->name }}" 
                                type="checkbox" 
                                class="h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-600">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="perm-{{ $perm->id }}" class="font-medium text-slate-600 cursor-pointer select-none">
                                    {{ str_replace($group . ' ', '', str_replace('view', 'View', str_replace('create', 'Create', str_replace('edit', 'Edit', str_replace('delete', 'Delete', ucfirst($perm->name)))))) }}
                                </label>
                            </div>
                        </div>
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
