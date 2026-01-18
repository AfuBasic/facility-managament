<div class="flex flex-col h-[calc(100vh-8rem)]" x-data x-init="$nextTick(() => { const c = document.getElementById('messages-container'); if(c) c.scrollTop = c.scrollHeight; })">
    <!-- Header -->
    <div class="bg-white border-b border-slate-200 px-6 py-4 flex items-center gap-4">
        <a href="{{ route('app.messages.index') }}" wire:navigate class="text-slate-400 hover:text-slate-600">
            <x-heroicon-o-arrow-left class="h-5 w-5" />
        </a>
        <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-medium">
            {{ substr($otherUser->name, 0, 1) }}
        </div>
        <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ $otherUser->name }}</h2>
            <p class="text-xs text-slate-500">{{ $otherUser->email }}</p>
        </div>
    </div>

    <!-- Messages -->
    <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-50" id="messages-container">
        @forelse($messages as $date => $dateMessages)
            <!-- Date Divider -->
            <div class="flex items-center gap-4">
                <div class="flex-1 border-t border-slate-200"></div>
                <span class="text-xs text-slate-400 font-medium">
                    {{ \Carbon\Carbon::parse($date)->format('M j, Y') }}
                </span>
                <div class="flex-1 border-t border-slate-200"></div>
            </div>

            @foreach($dateMessages as $message)
                @php $isMine = $message->sender_id === auth()->id(); @endphp
                <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[70%]">
                        <div class="{{ $isMine ? 'bg-teal-500 text-white rounded-t-2xl rounded-bl-2xl' : 'bg-white text-slate-900 rounded-t-2xl rounded-br-2xl border border-slate-200' }} px-4 py-3">
                            <p class="text-sm whitespace-pre-wrap">{{ $message->body }}</p>
                        </div>
                        <div class="flex items-center gap-2 mt-1 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                            <span class="text-xs text-slate-400">
                                {{ $message->created_at->format('g:i A') }}
                            </span>
                            @if($isMine)
                                @if($message->isRead())
                                    <span class="text-xs text-teal-500 font-medium">Seen</span>
                                @else
                                    <span class="text-xs text-slate-400">Sent</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @empty
            <div class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <x-heroicon-o-chat-bubble-left-ellipsis class="h-12 w-12 text-slate-300 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-slate-900 mb-1">Start the conversation</h3>
                    <p class="text-slate-500">Send a message to {{ $otherUser->name }}</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Input -->
    <div class="bg-white border-t border-slate-200 p-4">
        <form wire:submit="sendMessage" class="flex gap-3">
            <input 
                type="text" 
                wire:model="newMessage"
                class="flex-1 rounded-full border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:ring-teal-500"
                placeholder="Type a message..."
                autocomplete="off"
            >
            <button 
                type="submit" 
                class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-teal-500 text-white hover:bg-teal-600 transition-colors disabled:opacity-50"
            >
                <x-heroicon-o-paper-airplane class="h-5 w-5" />
            </button>
        </form>
        @error('newMessage')
            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
        @enderror
    </div>
</div>

