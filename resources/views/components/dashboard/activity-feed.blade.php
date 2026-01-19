@props(['activities'])

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col h-full overflow-hidden">
    <div class="p-6 border-b border-slate-50">
        <h3 class="font-semibold text-slate-900">Recent Activity</h3>
    </div>
    
    <div class="p-6 flex-1 overflow-y-auto">
        <div class="relative pl-6 border-l-2 border-slate-100 space-y-8">
            @forelse($activities as $activity)
                <div class="relative">
                    <!-- Dot -->
                    <div class="absolute -left-[31px] top-1 h-4 w-4 rounded-full border-2 border-white bg-indigo-500 shadow-sm"></div>
                    
                    <div class="flex flex-col space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-900">
                                {{ $activity->changedBy?->name ?? 'System' }}
                            </span>
                            <span class="text-xs text-slate-400">
                                {{ $activity->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-slate-600">
                            @if($activity->new_state)
                                Changed status of 
                                <a href="{{ route('app.work-orders.index') }}" class="font-medium text-indigo-600 hover:underline">
                                    {{ $activity->workOrder->workorder_serial ?? 'Work Order' }}
                                </a>
                                to <span class="font-medium text-slate-900">{{ ucfirst(str_replace('_', ' ', $activity->new_state)) }}</span>
                            @else
                                Updated 
                                <a href="{{ route('app.work-orders.index') }}" class="font-medium text-indigo-600 hover:underline">
                                    {{ $activity->workOrder->workorder_serial ?? 'Work Order' }}
                                </a>
                            @endif
                        </p>
                        
                        @if($activity->note)
                            <div class="mt-2 p-3 bg-slate-50 rounded-lg text-xs text-slate-500 italic border border-slate-100">
                                "{{ Str::limit($activity->note, 80) }}"
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-slate-400 text-sm">
                    No recent activity found.
                </div>
            @endforelse
        </div>
    </div>
</div>
