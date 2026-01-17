<div>
    <x-ui.page-header
        title="Notifications"
        description="View and manage all your notifications."
    >
        <x-slot:actions>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 focus:ring-teal-500">
                    <x-heroicon-o-check class="h-5 w-5 mr-2" />
                    Mark all as read
                </button>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filters --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex gap-2">
            <button wire:click="$set('filter', 'all')"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filter === 'all' ? 'bg-teal-600 text-white' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                All
            </button>
            <button wire:click="$set('filter', 'unread')"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filter === 'unread' ? 'bg-teal-600 text-white' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                Unread
                @if($unreadCount > 0)
                    <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full {{ $filter === 'unread' ? 'bg-white/20 text-white' : 'bg-teal-100 text-teal-700' }}">
                        {{ $unreadCount }}
                    </span>
                @endif
            </button>
            <button wire:click="$set('filter', 'read')"
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $filter === 'read' ? 'bg-teal-600 text-white' : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50' }}">
                Read
            </button>
        </div>

        @if($filter === 'read' && $notifications->count() > 0)
            <button @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                        detail: {
                            title: 'Clear Read Notifications',
                            message: 'Are you sure you want to delete all read notifications? This action cannot be undone.',
                            confirmText: 'Delete All',
                            cancelText: 'Cancel',
                            variant: 'danger',
                            action: () => $wire.deleteAllRead()
                        }
                    }))"
                    class="inline-flex items-center text-sm text-red-600 hover:text-red-700 font-medium">
                <x-heroicon-o-trash class="h-4 w-4 mr-1" />
                Clear read notifications
            </button>
        @endif
    </div>

    {{-- Notifications List --}}
    <x-ui.card class="overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($notifications as $notification)
                <div wire:key="notification-{{ $notification->id }}"
                     class="px-6 py-4 hover:bg-slate-50 transition-colors {{ $notification->read_at ? 'bg-slate-50/50' : 'bg-white' }}">
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div class="shrink-0">
                            @php
                                $iconColor = match($notification->data['color'] ?? 'slate') {
                                    'teal' => 'text-teal-500 bg-teal-50',
                                    'red' => 'text-red-500 bg-red-50',
                                    'blue' => 'text-blue-500 bg-blue-50',
                                    'amber' => 'text-amber-500 bg-amber-50',
                                    default => 'text-slate-500 bg-slate-100',
                                };
                            @endphp
                            <div class="h-10 w-10 rounded-full {{ $iconColor }} flex items-center justify-center">
                                @switch($notification->data['icon'] ?? 'bell')
                                    @case('clipboard-document-list')
                                        <x-heroicon-o-clipboard-document-list class="h-5 w-5" />
                                        @break
                                    @case('exclamation-triangle')
                                        <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
                                        @break
                                    @case('arrow-path')
                                        <x-heroicon-o-arrow-path class="h-5 w-5" />
                                        @break
                                    @case('check-circle')
                                        <x-heroicon-o-check-circle class="h-5 w-5" />
                                        @break
                                    @case('check-badge')
                                        <x-heroicon-o-check-badge class="h-5 w-5" />
                                        @break
                                    @case('x-circle')
                                        <x-heroicon-o-x-circle class="h-5 w-5" />
                                        @break
                                    @case('archive-box')
                                        <x-heroicon-o-archive-box class="h-5 w-5" />
                                        @break
                                    @case('chat-bubble-left-ellipsis')
                                        <x-heroicon-o-chat-bubble-left-ellipsis class="h-5 w-5" />
                                        @break
                                    @case('pause-circle')
                                        <x-heroicon-o-pause-circle class="h-5 w-5" />
                                        @break
                                    @case('play-circle')
                                        <x-heroicon-o-play-circle class="h-5 w-5" />
                                        @break
                                    @case('arrow-path-rounded-square')
                                        <x-heroicon-o-arrow-path-rounded-square class="h-5 w-5" />
                                        @break
                                    @default
                                        <x-heroicon-o-bell class="h-5 w-5" />
                                @endswitch
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-900 {{ $notification->read_at ? '' : 'font-medium' }}">
                                {{ $notification->data['message'] ?? 'New notification' }}
                            </p>
                            @if(isset($notification->data['title']))
                                <p class="text-sm text-slate-500 mt-0.5">
                                    {{ $notification->data['title'] }}
                                </p>
                            @endif
                            <p class="text-xs text-slate-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                                @if($notification->read_at)
                                    <span class="text-slate-300 mx-1">&bull;</span>
                                    <span>Read {{ $notification->read_at->diffForHumans() }}</span>
                                @endif
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="shrink-0 flex items-center gap-2">
                            @if(!$notification->read_at)
                                <div class="h-2 w-2 rounded-full bg-teal-500" title="Unread"></div>
                            @endif

                            @if(isset($notification->data['route']))
                                <button wire:click="markAsRead('{{ $notification->id }}', true)"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-teal-600 bg-teal-50 rounded-lg hover:bg-teal-100 transition-colors">
                                    {{ $notification->data['route_name'] ?? 'View' }}
                                </button>
                            @endif

                            @if(!$notification->read_at)
                                <button wire:click="markAsRead('{{ $notification->id }}')"
                                        class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
                                        title="Mark as read">
                                    <x-heroicon-o-check class="h-4 w-4" />
                                </button>
                            @endif

                            <button @click="window.dispatchEvent(new CustomEvent('confirm-action', {
                                        detail: {
                                            title: 'Delete Notification',
                                            message: 'Are you sure you want to delete this notification?',
                                            confirmText: 'Delete',
                                            cancelText: 'Cancel',
                                            variant: 'danger',
                                            action: () => $wire.deleteNotification('{{ $notification->id }}')
                                        }
                                    }))"
                                    class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Delete notification">
                                <x-heroicon-o-trash class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    @if($filter === 'unread')
                        <x-heroicon-o-check-circle class="h-12 w-12 text-teal-300 mx-auto mb-3" />
                        <h3 class="text-sm font-medium text-slate-900">You're all caught up!</h3>
                        <p class="text-sm text-slate-500 mt-1">No unread notifications.</p>
                    @elseif($filter === 'read')
                        <x-heroicon-o-inbox class="h-12 w-12 text-slate-300 mx-auto mb-3" />
                        <h3 class="text-sm font-medium text-slate-900">No read notifications</h3>
                        <p class="text-sm text-slate-500 mt-1">Read notifications will appear here.</p>
                    @else
                        <x-heroicon-o-bell-slash class="h-12 w-12 text-slate-300 mx-auto mb-3" />
                        <h3 class="text-sm font-medium text-slate-900">No notifications yet</h3>
                        <p class="text-sm text-slate-500 mt-1">You'll see notifications here when there's activity on your work orders.</p>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </x-ui.card>
</div>
