<div class="flex items-center" wire:key="message-icon-{{ $clientId }}">
    <a href="{{ route('app.messages.index') }}"
       wire:navigate
       class="relative inline-flex items-center justify-center p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
       title="Messages">
        <x-heroicon-o-envelope class="h-6 w-6" />

        @if($unreadCount > 0)
        <span class="absolute -top-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white">
            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
        </span>
        @endif
    </a>
</div>
