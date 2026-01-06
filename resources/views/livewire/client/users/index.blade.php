<div>
    <x-ui.page-header 
    title="Users Management" 
    description="Manage team members and their access levels."
    >
    @can('create users')
    <x-slot:actions>
    <x-ui.button @click="$wire.showInviteModal = true">
        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
        </svg>
        Invite User
    </x-ui.button>
</x-slot:actions>
@endcan
</x-ui.page-header>
@if($roles->isEmpty())
<div class="rounded-lg bg-amber-50 p-4 border border-amber-200 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-amber-800">No Roles Available</h3>
            <div class="mt-2 text-sm text-amber-700">
                <p>You need to create roles before you can invite users. <a href="{{ route('app.roles') }}" class="font-medium underline hover:text-amber-600">Go to Roles</a>.</p>
            </div>
        </div>
    </div>
</div>
@endif

<x-ui.card class="overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">User</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($memberships as $membership)
                <tr wire:key="membership-{{ $membership->id }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-700 font-bold text-sm">
                                {{ $membership->user->initials() }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-slate-900">{{ $membership->user->name }}</div>
                                <div class="text-sm text-slate-500">{{ $membership->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @foreach($membership->user->roles as $role)
                        <x-ui.badge variant="neutral">{{ ucwords($role->name) }}</x-ui.badge>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($membership->status === 'accepted')
                        <x-ui.badge variant="success">Active</x-ui.badge>
                        @elseif($membership->status === 'pending')
                        <x-ui.badge variant="warning">Pending</x-ui.badge>
                        @elseif($membership->status === 'expired')
                        <x-ui.badge variant="danger">Expired</x-ui.badge>
                        @elseif($membership->status === 'password_reset')
                        <x-ui.badge variant="primary">Resetting</x-ui.badge>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            @can('edit users')
                            <!-- Edit Role Button -->
                            <button 
                                wire:click="editRole({{ $membership->id }})" 
                                class="p-1.5 text-slate-400 hover:text-teal-600 hover:bg-teal-50 rounded transition-colors"
                                title="Edit Role"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                            @endcan
                            
                            @if($membership->status === 'expired' || $membership->status === 'password_reset')
                            <!-- Resend Invite Button -->
                            <button 
                                wire:click="resend({{ $membership->id }})" 
                                wire:loading.attr="disabled" 
                                wire:target="resend({{ $membership->id }})" 
                                class="p-1.5 text-slate-400 hover:text-teal-600 hover:bg-teal-50 rounded transition-colors disabled:opacity-50"
                                title="Resend Invite"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </button>
                            @endif
                            
                            @if($membership->status === 'accepted')
                            @can('edit users')
                            <!-- Reset Account Button -->
                            <button 
                                wire:loading.attr="disabled" 
                                wire:target="resetAccount({{ $membership->id }})" 
                                @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                    detail: {
                                        title: 'Reset Account',
                                        message: 'This will reset the user\'s password and require them to setup their account again. Continue?',
                                        confirmText: 'Reset Account',
                                        cancelText: 'Cancel',
                                        variant: 'warning',
                                        action: () => $wire.resetAccount({{ $membership->id }})
                                    }
                                }))"
                                class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors disabled:opacity-50"
                                title="Reset Account"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                            </button>
                            @endcan
                            @endif
                            
                            @can('delete users')
                            <!-- Delete Button -->
                            <button 
                                wire:loading.attr="disabled" 
                                wire:target="delete({{ $membership->id }})" 
                                @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                    detail: {
                                        title: 'Remove User',
                                        message: 'Are you sure you want to remove this user? This action cannot be undone.',
                                        confirmText: 'Remove User',
                                        cancelText: 'Cancel',
                                        variant: 'danger',
                                        action: () => $wire.delete({{ $membership->id }})
                                    }
                                }))"
                                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors disabled:opacity-50"
                                title="Remove User"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                        No users found. Invite your first team member!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $memberships->links() }}
    </div>
</x-ui.card>

<!-- Invite User Modal -->
<x-ui.modal show="showInviteModal" title="Invite New User">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
            <input type="email" wire:model="email" class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500">
            @error('email') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
            <select wire:model="role" class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500">
                <option value="">Select a role</option>
                @foreach($roles as $role)
                <option value="{{ $role->name }}">{{ ucwords($role->name) }}</option>
                @endforeach
            </select>
            @error('role') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>
    </div>
    
    <x-slot:footer>
    <x-ui.button variant="secondary" @click="show = false">Cancel</x-ui.button>
    <x-ui.button wire:click="invite" :disabled="$roles->isEmpty()" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="invite">Send Invitation</span>
        <span wire:loading wire:target="invite">Sending...</span>
    </x-ui.button>
</x-slot:footer>
</x-ui.modal>

<!-- Edit Role Modal -->
<x-ui.modal show="showEditRoleModal" title="Edit User Role">
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
            <select wire:model="selectedRole" class="p-2 border w-full rounded-lg border-slate-200 focus:border-teal-500 focus:ring-teal-500">
                <option value="">Select a role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ ucwords($role->name) }}</option>
                @endforeach
            </select>
            @error('selectedRole') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>
    </div>
    
    <x-slot:footer>
        <x-ui.button variant="secondary" @click="$wire.showEditRoleModal = false">Cancel</x-ui.button>
        <x-ui.button wire:click="updateRole" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="updateRole">Update Role</span>
            <span wire:loading wire:target="updateRole">Updating...</span>
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>
</div>
