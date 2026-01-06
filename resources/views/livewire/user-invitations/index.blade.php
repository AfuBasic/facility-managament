<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">My Invitations</h1>
        <p class="text-slate-500 mt-2">View and manage your organization invitations.</p>
    </div>

    <!-- Tabs -->
    <div class="mb-6 border-b border-slate-200">
        <nav class="-mb-px flex space-x-8">
            <button 
                wire:click="setTab('pending')"
                class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'pending' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
            >
                Pending Invitations
                @if($invitations->count() > 0)
                    <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-800">
                        {{ $invitations->count() }}
                    </span>
                @endif
            </button>
            <button 
                wire:click="setTab('history')"
                class="pb-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'history' ? 'border-teal-500 text-teal-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}"
            >
                Invitation History
            </button>
        </nav>
    </div>

    <!-- Pending Invitations Tab -->
    <div x-show="$wire.activeTab === 'pending'" x-cloak>
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

    <!-- Invitation History Tab -->
    <div x-show="$wire.activeTab === 'history'" x-cloak style="display: none;">
        @if($invitationHistory->count() > 0)
            <div class="space-y-3">
                @foreach($invitationHistory as $log)
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-slate-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-lg bg-slate-50 flex items-center justify-center text-slate-600">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-slate-900">{{ $log->clientAccount->name }}</h4>
                                        <div class="flex items-center gap-4 mt-1 text-xs text-slate-500">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                                </svg>
                                                Role: <span class="font-medium text-slate-700">{{ ucfirst($log->role_name) }}</span>
                                            </span>
                                            <span>Invited: {{ $log->invited_at->format('M d, Y h:i a') }}</span>
                                            @if($log->accepted_at)
                                                <span>Accepted: {{ $log->accepted_at->format('M d, Y h:i a') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($log->status === 'accepted')
                                    <x-ui.badge variant="success">Accepted</x-ui.badge>
                                @elseif($log->status === 'pending')
                                    <x-ui.badge variant="warning">Pending</x-ui.badge>
                                @elseif($log->status === 'expired')
                                    <x-ui.badge variant="danger">Expired</x-ui.badge>
                                @else
                                    <x-ui.badge variant="secondary">{{ ucfirst($log->status) }}</x-ui.badge>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300">
                <div class="mx-auto h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900">No invitation history</h3>
                <p class="text-slate-500 mt-1 text-sm">You haven't received any invitations yet.</p>
            </div>
        @endif
    </div>
</div>
