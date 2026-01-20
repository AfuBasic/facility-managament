@props(['conversations' => [], 'unreadCount' => 0])

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <x-heroicon-o-chat-bubble-left-right class="h-5 w-5 text-teal-500" />
            <h3 class="font-semibold text-slate-900">Messages</h3>
            @if($unreadCount > 0)
                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-full">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </div>
        <a href="{{ route('app.messages.index') }}" wire:navigate class="text-xs font-medium text-teal-600 hover:text-teal-700">
            View all →
        </a>
    </div>

    <div class="divide-y divide-slate-100">
        @forelse($conversations as $conversation)
            <a 
                href="{{ route('app.messages.index', ['conversation' => $conversation['hashid']]) }}" 
                wire:navigate
                class="flex items-center gap-3 p-4 hover:bg-slate-50 transition-colors"
            >
                {{-- Avatar --}}
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white font-semibold text-sm">
                        {{ strtoupper(substr($conversation['other_user']->name ?? 'U', 0, 1)) }}
                    </div>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-0.5">
                        <p class="text-sm font-medium text-slate-900 truncate">
                            {{ $conversation['other_user']->name ?? 'Unknown User' }}
                        </p>
                        @if($conversation['latest_message'])
                            <span class="text-[10px] text-slate-400 whitespace-nowrap">
                                {{ $conversation['latest_message']->created_at->diffForHumans(null, true) }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 truncate">
                        @if($conversation['latest_message'])
                            {{ Str::limit($conversation['latest_message']->content, 50) }}
                        @else
                            <span class="italic text-slate-400">No messages yet</span>
                        @endif
                    </p>
                </div>

                {{-- Unread Badge --}}
                @if($conversation['unread_count'] > 0)
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-teal-500 rounded-full">
                            {{ $conversation['unread_count'] > 9 ? '9+' : $conversation['unread_count'] }}
                        </span>
                    </div>
                @endif
            </a>
        @empty
            <div class="p-6 text-center">
                <x-heroicon-o-chat-bubble-left-ellipsis class="h-8 w-8 mx-auto text-slate-300 mb-2" />
                <p class="text-sm text-slate-500">No conversations yet</p>
                <a href="{{ route('app.messages.index') }}" wire:navigate class="text-sm text-teal-600 hover:text-teal-700 mt-1 inline-block">
                    Start a conversation →
                </a>
            </div>
        @endforelse
    </div>
</div>
