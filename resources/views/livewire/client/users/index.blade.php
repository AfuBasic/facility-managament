<div>
    <x-ui.page-header 
        title="Users Management" 
        description="Manage team members and their access levels."
    >
        <x-slot:actions>
            <x-ui.button @click="$wire.showInviteModal = true">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                </svg>
                Invite User
            </x-ui.button>
        </x-slot:actions>
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
                            <div class="flex items-center justify-end gap-3">
                                @if($membership->status === 'expired' || $membership->status === 'password_reset')
                                    <button wire:click="resend({{ $membership->id }})" wire:loading.attr="disabled" wire:target="resend({{ $membership->id }})" class="text-teal-600 hover:text-teal-900 disabled:opacity-50">Resend Invite</button>
                                @endif
                                
                                @if($membership->status === 'accepted')
                                    <button wire:click="resetAccount({{ $membership->id }})" wire:loading.attr="disabled" wire:target="resetAccount({{ $membership->id }})" wire:confirm="This will reset the user's password and require them to setup their account again. Continue?" class="text-indigo-600 hover:text-indigo-900 disabled:opacity-50">Reset Account</button>
                                @endif

                                <button wire:click="delete({{ $membership->id }})" wire:loading.attr="disabled" wire:target="delete({{ $membership->id }})" wire:confirm="Are you sure you want to remove this user?" class="text-red-600 hover:text-red-900 disabled:opacity-50">Remove</button>
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
</div>
