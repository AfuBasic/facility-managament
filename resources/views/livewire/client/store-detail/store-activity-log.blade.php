<div>
    {{-- Controls --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between">
        <div class="relative max-w-sm w-full">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <x-heroicon-o-magnifying-glass class="h-5 w-5 text-slate-400" />
            </div>
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                class="block w-full rounded-lg border border-slate-300 pl-10 py-2 text-sm focus:border-teal-500 focus:ring-teal-500" 
                placeholder="Search by asset, serial, or user..."
            >
        </div>
        
        <div class="w-full sm:w-48">
            <select wire:model.live="actionFilter" class="block w-full rounded-lg border border-slate-300 py-2 text-sm focus:border-teal-500 focus:ring-teal-500">
                <option value="">All Actions</option>
                <option value="restock">Restock</option>
                <option value="checkout">Check Out</option>
                <option value="checkin">Check In</option>
                <option value="create">Created</option>
                <option value="update">Updated</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Action</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Asset</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Performed By</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $log->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium capitalize
                                    {{ $log->action_type === 'restock' ? 'bg-green-100 text-green-800' : 
                                      ($log->action_type === 'checkout' ? 'bg-blue-100 text-blue-800' : 
                                      ($log->action_type === 'checkin' ? 'bg-orange-100 text-orange-800' : 'bg-slate-100 text-slate-800')) }}">
                                    {{ $log->action_type ?? 'Update' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">{{ $log->asset->name ?? 'Unknown Asset' }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $log->asset->serial ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                                {{ $log->performedBy->name ?? 'System' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                @if($log->units > 1 || $log->action_type === 'restock')
                                    <div>Quantity: {{ $log->units }}</div>
                                @endif
                                @if($log->targetUser)
                                    <div>To: {{ $log->targetUser->name }}</div>
                                @endif
                                @if($log->note)
                                    <div class="italic text-xs mt-1">"{{ $log->note }}"</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                                No activity logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
