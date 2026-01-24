<div>
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Notifications</h1>
            <p class="text-slate-500 mt-1">
                @if($unreadCount > 0)
                    You have {{ $unreadCount }} unread {{ Str::plural('notification', $unreadCount) }}
                @else
                    All caught up!
                @endif
            </p>
        </div>

        @if($unreadCount > 0)
            <button wire:click="markAllAsRead" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Mark all as read
            </button>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        @forelse($notifications as $notification)
            <div wire:key="notification-{{ $notification->id }}"
                 class="p-4 border-b border-slate-100 last:border-b-0 hover:bg-slate-50 transition-colors {{ !$notification->read_at ? 'bg-blue-50/50' : '' }}">
                <div class="flex items-start gap-4">
                    <!-- Icon -->
                    <div class="shrink-0">
                        @if(($notification->data['registration_method'] ?? '') === 'google')
                            <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-5 h-5" viewBox="0 0 24 24">
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                </svg>
                            </div>
                        @else
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </p>
                                <p class="text-sm text-slate-600 mt-0.5">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                            </div>
                            @if(!$notification->read_at)
                                <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    New
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-xs text-slate-400">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                            @if(!$notification->read_at)
                                <button wire:click="markAsRead('{{ $notification->id }}')" class="text-xs text-blue-600 hover:text-blue-800">
                                    Mark as read
                                </button>
                            @endif
                            @if($notification->data['url'] ?? null)
                                <a href="{{ $notification->data['url'] }}" wire:navigate class="text-xs text-slate-600 hover:text-slate-800">
                                    View details
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-slate-300 mx-auto mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
                <p class="text-slate-500">No notifications yet.</p>
            </div>
        @endforelse

        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
