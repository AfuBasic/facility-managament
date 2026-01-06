<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">My Invitations</h1>
        <p class="text-slate-500 mt-2">View and manage your pending organization invitations.</p>
    </div>

    @if($invitations->count() > 0)
        <div class="space-y-4">
            @foreach($invitations as $invitation)
                <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="h-12 w-12 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900">
                                        {{ $invitation->clientAccount->name }}
                                    </h3>
                                    <p class="text-sm text-slate-500">
                                        Invited {{ $invitation->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($invitation->status === 'pending')
                                <x-ui.badge variant="warning">Pending</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger">Expired</x-ui.badge>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex gap-3">
                        @if($invitation->status === 'pending')
                            <x-ui.button 
                                @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                    detail: {
                                        title: 'Accept Invitation',
                                        message: 'Are you sure you want to accept the invitation to join {{ $invitation->clientAccount->name }}?',
                                        confirmText: 'Accept',
                                        cancelText: 'Cancel',
                                        variant: 'info',
                                        action: () => $wire.accept({{ $invitation->id }})
                                    }
                                }))"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Accept Invitation
                            </x-ui.button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300">
            <div class="mx-auto h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-slate-900">No pending invitations</h3>
            <p class="text-slate-500 mt-1 text-sm">You don't have any pending organization invitations at the moment.</p>
        </div>
    @endif
</div>
