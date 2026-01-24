<div>
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Users</h1>
            <p class="text-slate-500 mt-1">Manage all registered users</p>
        </div>

        <!-- Search -->
        <div class="relative">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search users..."
                   class="w-full sm:w-72 pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-400 absolute left-3 top-2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Organizations</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors" wire:key="user-{{ $user->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-medium text-sm">
                                        {{ $user->initials() ?: 'U' }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-slate-900">{{ $user->name ?: 'No name' }}</p>
                                        <p class="text-sm text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-600">{{ $user->client_memberships_count }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-600">{{ $user->created_at->format('M j, Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->suspended_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Suspended
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($user->suspended_at)
                                        <button wire:click="activate({{ $user->id }})" wire:loading.attr="disabled"
                                                class="text-green-600 hover:text-green-800 text-sm font-medium">
                                            Activate
                                        </button>
                                    @else
                                        <button wire:click="suspend({{ $user->id }})" wire:loading.attr="disabled"
                                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Suspend
                                        </button>
                                    @endif
                                    <span class="text-slate-300">|</span>
                                    <a href="{{ route('admin.users.impersonate', $user) }}" target="_blank"
                                       class="text-slate-600 hover:text-slate-800 text-sm font-medium">
                                        Impersonate
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                @if($search)
                                    No users found matching "{{ $search }}"
                                @else
                                    No users yet.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
