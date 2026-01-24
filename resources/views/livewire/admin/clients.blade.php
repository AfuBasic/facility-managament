<div>
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Organizations</h1>
            <p class="text-slate-500 mt-1">View all registered organizations</p>
        </div>

        <!-- Search -->
        <div class="relative">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search organizations..."
                   class="w-full sm:w-72 pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-400 absolute left-3 top-2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Organization</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Members</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($clients as $client)
                        <tr class="hover:bg-slate-50 transition-colors" wire:key="client-{{ $client->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 font-medium text-sm">
                                        {{ strtoupper(substr($client->name, 0, 2)) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-slate-900">{{ $client->name }}</p>
                                        @if($client->address)
                                            <p class="text-sm text-slate-500 truncate max-w-xs">{{ $client->address }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-600">{{ $client->memberships_count }} {{ Str::plural('member', $client->memberships_count) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    @if($client->notification_email)
                                        <p class="text-slate-600">{{ $client->notification_email }}</p>
                                    @endif
                                    @if($client->phone)
                                        <p class="text-slate-500">{{ $client->phone }}</p>
                                    @endif
                                    @if(!$client->notification_email && !$client->phone)
                                        <p class="text-slate-400">-</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-600">{{ $client->created_at->format('M j, Y') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                @if($search)
                                    No organizations found matching "{{ $search }}"
                                @else
                                    No organizations yet.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>
