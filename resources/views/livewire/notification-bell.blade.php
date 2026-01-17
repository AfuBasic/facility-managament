<div class="relative" x-data="{ open: false }">
    <!-- Bell Icon Button -->
    <button @click="open = !open" 
            type="button" 
            class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
            title="Notifications">
        <x-heroicon-o-bell class="h-6 w-6" />
        
        <!-- Unread Badge -->
        @if($unreadCount > 0)
        <span class="absolute -top-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open" 
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute right-0 z-50 mt-2 w-80 origin-top-right rounded-xl bg-white shadow-lg ring-1 ring-slate-900/5 focus:outline-none overflow-hidden"
         x-cloak>
        
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50">
            <h3 class="text-sm font-semibold text-slate-900">Notifications</h3>
            @if($unreadCount > 0)
            <button wire:click="markAllAsRead" class="text-xs text-teal-600 hover:text-teal-700 font-medium">
                Mark all as read
            </button>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto divide-y divide-slate-100">
            @forelse($notifications as $notification)
                <div wire:key="notification-{{ $notification->id }}"
                     class="px-4 py-3 hover:bg-slate-50 transition-colors cursor-pointer {{ $notification->read_at ? 'opacity-60' : '' }}"
                     wire:click="markAsRead('{{ $notification->id }}', true)">
                    <div class="flex gap-3">
                        <!-- Icon -->
                        <div class="shrink-0">
                            @php
                                $iconColor = match($notification->data['color'] ?? 'slate') {
                                    'teal' => 'text-teal-500 bg-teal-50',
                                    'red' => 'text-red-500 bg-red-50',
                                    'blue' => 'text-blue-500 bg-blue-50',
                                    'amber' => 'text-amber-500 bg-amber-50',
                                    default => 'text-slate-500 bg-slate-50',
                                };
                            @endphp
                            <div class="h-8 w-8 rounded-full {{ $iconColor }} flex items-center justify-center">
                                @switch($notification->data['icon'] ?? 'bell')
                                    @case('clipboard-document-list')
                                        <x-heroicon-o-clipboard-document-list class="h-4 w-4" />
                                        @break
                                    @case('exclamation-triangle')
                                        <x-heroicon-o-exclamation-triangle class="h-4 w-4" />
                                        @break
                                    @case('arrow-path')
                                        <x-heroicon-o-arrow-path class="h-4 w-4" />
                                        @break
                                    @case('check-circle')
                                        <x-heroicon-o-check-circle class="h-4 w-4" />
                                        @break
                                    @case('check-badge')
                                        <x-heroicon-o-check-badge class="h-4 w-4" />
                                        @break
                                    @case('x-circle')
                                        <x-heroicon-o-x-circle class="h-4 w-4" />
                                        @break
                                    @case('archive-box')
                                        <x-heroicon-o-archive-box class="h-4 w-4" />
                                        @break
                                    @case('chat-bubble-left-ellipsis')
                                        <x-heroicon-o-chat-bubble-left-ellipsis class="h-4 w-4" />
                                        @break
                                    @case('pause-circle')
                                        <x-heroicon-o-pause-circle class="h-4 w-4" />
                                        @break
                                    @case('play-circle')
                                        <x-heroicon-o-play-circle class="h-4 w-4" />
                                        @break
                                    @case('arrow-path-rounded-square')
                                        <x-heroicon-o-arrow-path-rounded-square class="h-4 w-4" />
                                        @break
                                    @default
                                        <x-heroicon-o-bell class="h-4 w-4" />
                                @endswitch
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-900 {{ $notification->read_at ? '' : 'font-medium' }}">
                                {{ $notification->data['message'] ?? 'New notification' }}
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Unread Indicator -->
                        @if(!$notification->read_at)
                        <div class="shrink-0 self-center">
                            <div class="h-2 w-2 rounded-full bg-teal-500"></div>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <x-heroicon-o-bell-slash class="h-8 w-8 text-slate-300 mx-auto mb-2" />
                    <p class="text-sm text-slate-500">No notifications yet</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
