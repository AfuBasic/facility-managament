<div class="flex h-[calc(100vh-12rem)] bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm"
     x-data="{ mobileShowChat: false }"
     x-on:url-changed.window="history.pushState({}, '', $event.detail.url)">

    <!-- Conversations Sidebar -->
    <div class="w-full md:w-80 lg:w-96 border-r border-slate-200 flex flex-col bg-slate-50"
         :class="{ 'hidden md:flex': mobileShowChat }">

        <!-- Sidebar Header -->
        <div class="p-4 border-b border-slate-200 bg-white">
            <div class="flex items-center justify-between mb-3">
                <h1 class="text-lg font-bold text-slate-900">Messages</h1>
                <button wire:click="openNewMessage"
                        class="p-2 rounded-full bg-teal-500 text-white hover:bg-teal-600 transition-all hover:scale-105 shadow-md">
                    <x-heroicon-s-plus class="h-5 w-5" />
                </button>
            </div>

            <!-- Search -->
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search conversations..."
                       class="w-full pl-9 pr-4 py-2 text-sm rounded-full border border-slate-200 bg-slate-50 focus:bg-white focus:border-teal-400 focus:ring-2 focus:ring-teal-100 transition-all">
            </div>
        </div>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto">
            @forelse($this->conversations as $conversation)
                <button wire:click="selectConversation('{{ $conversation->hashid }}')"
                        wire:key="conversation-{{ $conversation->id }}"
                        @click="mobileShowChat = true"
                        class="w-full flex items-center gap-3 p-4 hover:bg-white border-b border-slate-100 transition-all text-left {{ $activeConversationId === $conversation->id ? 'bg-white border-l-4 border-l-teal-500' : '' }}">

                    <!-- Avatar -->
                    <div class="relative shrink-0">
                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                            {{ substr($conversation->other_user->name, 0, 1) }}
                        </div>
                        @if($conversation->unread_count > 0)
                            <span class="absolute -top-1 -right-1 h-5 w-5 flex items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold ring-2 ring-slate-50">
                                {{ $conversation->unread_count > 9 ? '9+' : $conversation->unread_count }}
                            </span>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-0.5">
                            <span class="font-semibold text-slate-900 truncate {{ $conversation->unread_count > 0 ? 'text-teal-700' : '' }}">
                                {{ $conversation->other_user->name }}
                            </span>
                            @if($conversation->latestMessage)
                                <span class="text-xs text-slate-400 shrink-0 ml-2">
                                    {{ $conversation->latestMessage->created_at->shortAbsoluteDiffForHumans() }}
                                </span>
                            @endif
                        </div>
                        @if($conversation->latestMessage)
                            <p class="text-sm text-slate-500 truncate {{ $conversation->unread_count > 0 ? 'font-medium text-slate-700' : '' }}">
                                @if($conversation->latestMessage->sender_id === auth()->id())
                                    <span class="text-slate-400">You: </span>
                                @endif
                                {{ Str::limit($conversation->latestMessage->body, 35) }}
                            </p>
                        @else
                            <p class="text-sm text-slate-400 italic">Start the conversation...</p>
                        @endif
                    </div>
                </button>
            @empty
                <div class="p-8 text-center">
                    <div class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                        <x-heroicon-o-chat-bubble-left-right class="h-8 w-8 text-slate-400" />
                    </div>
                    <p class="text-slate-500 font-medium">No conversations yet</p>
                    <p class="text-sm text-slate-400 mt-1">Start a new message!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Chat Panel -->
    <div class="flex-1 flex flex-col bg-white"
         :class="{ 'hidden md:flex': !mobileShowChat && !{{ $activeConversationId ? 'true' : 'false' }} }">

        @if($this->activeConversation)
            <!-- Chat Header -->
            <div class="px-6 py-4 border-b border-slate-200 flex items-center gap-4 bg-white">
                <button @click="mobileShowChat = false" class="md:hidden text-slate-400 hover:text-slate-600">
                    <x-heroicon-o-arrow-left class="h-5 w-5" />
                </button>
                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white font-bold shadow-sm">
                    {{ substr($this->otherUser->name, 0, 1) }}
                </div>
                <div class="flex-1">
                    <h2 class="font-semibold text-slate-900">{{ $this->otherUser->name }}</h2>
                    <p class="text-xs text-slate-500">{{ $this->otherUser->email }}</p>
                </div>
            </div>

            <!-- Messages -->
            <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gradient-to-b from-slate-50 to-white"
                 id="messages-container"
                 x-data="{
                     scrollToBottom() {
                         this.$el.scrollTop = this.$el.scrollHeight;
                     }
                 }"
                 x-init="$nextTick(() => scrollToBottom())"
                 x-on:scroll-to-bottom.window="$nextTick(() => scrollToBottom())"
                 wire:key="messages-{{ $activeConversationId }}">

                @forelse($this->activeMessages as $date => $dateMessages)
                    <!-- Date Divider -->
                    <div class="flex items-center gap-3 my-6">
                        <div class="flex-1 h-px bg-slate-200"></div>
                        <span class="text-xs text-slate-400 font-medium px-3 py-1 bg-slate-100 rounded-full">
                            @if(\Carbon\Carbon::parse($date)->isToday())
                                Today
                            @elseif(\Carbon\Carbon::parse($date)->isYesterday())
                                Yesterday
                            @else
                                {{ \Carbon\Carbon::parse($date)->format('M j, Y') }}
                            @endif
                        </span>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    @foreach($dateMessages as $message)
                        @php $isMine = $message->sender_id === auth()->id(); @endphp
                        <div wire:key="message-{{ $message->id }}" class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%] group">
                                <div class="{{ $isMine
                                    ? 'bg-gradient-to-br from-teal-500 to-emerald-500 text-white rounded-2xl rounded-br-md shadow-md'
                                    : 'bg-white text-slate-900 rounded-2xl rounded-bl-md shadow-sm border border-slate-100' }} px-4 py-2.5">
                                    <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ $message->body }}</p>
                                </div>
                                <div class="flex items-center gap-1.5 mt-1 {{ $isMine ? 'justify-end' : 'justify-start' }} opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-xs text-slate-400">{{ $message->created_at->format('g:i A') }}</span>
                                    @if($isMine)
                                        @if($message->isRead())
                                            <x-heroicon-s-check-circle class="h-3.5 w-3.5 text-teal-500" />
                                        @else
                                            <x-heroicon-o-check class="h-3.5 w-3.5 text-slate-400" />
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="flex-1 flex items-center justify-center py-20">
                        <div class="text-center">
                            <div class="h-20 w-20 rounded-full bg-gradient-to-br from-teal-100 to-emerald-100 flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-chat-bubble-left-ellipsis class="h-10 w-10 text-teal-500" />
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-1">Start the conversation</h3>
                            <p class="text-slate-500">Send your first message to {{ $this->otherUser->name }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            <div class="p-4 border-t border-slate-200 bg-white">
                <form wire:submit="sendMessage" class="flex items-center gap-3">
                    <input type="text"
                           wire:model="newMessage"
                           placeholder="Type a message..."
                           autocomplete="off"
                           class="flex-1 px-5 py-3 rounded-full border border-slate-200 bg-slate-50 focus:bg-white focus:border-teal-400 focus:ring-2 focus:ring-teal-100 transition-all text-sm">
                    <button type="submit"
                            class="h-11 w-11 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 text-white flex items-center justify-center hover:shadow-lg hover:scale-105 transition-all disabled:opacity-50 disabled:hover:scale-100">
                        <x-heroicon-s-paper-airplane class="h-5 w-5" />
                    </button>
                </form>
            </div>
        @else
            <!-- Empty State -->
            <div class="flex-1 flex items-center justify-center p-8">
                <div class="text-center max-w-md">
                    <div class="h-24 w-24 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center mx-auto mb-6">
                        <x-heroicon-o-chat-bubble-left-right class="h-12 w-12 text-slate-400" />
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 mb-2">Select a conversation</h2>
                    <p class="text-slate-500 mb-6">Choose from your existing conversations or start a new one.</p>
                    <button wire:click="openNewMessage"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-gradient-to-r from-teal-500 to-emerald-500 text-white font-medium hover:shadow-lg hover:scale-105 transition-all">
                        <x-heroicon-s-plus class="h-5 w-5" />
                        New Message
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- New Message Modal -->
    <x-ui.modal show="showNewMessageModal" title="New Message" maxWidth="md">
        <div class="space-y-4">
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                <input type="text"
                       wire:model.live.debounce.300ms="userSearch"
                       placeholder="Search by name..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-slate-200 focus:border-teal-400 focus:ring-2 focus:ring-teal-100 text-sm">
            </div>

            <div class="max-h-72 overflow-y-auto divide-y divide-slate-100 rounded-lg border border-slate-200">
                @forelse($this->availableUsers as $user)
                    <button wire:click="startConversation({{ $user->id }})"
                            wire:key="user-{{ $user->id }}"
                            class="w-full flex items-center gap-3 p-3 hover:bg-teal-50 transition-colors text-left">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-teal-400 to-emerald-500 flex items-center justify-center text-white font-bold shadow-sm">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">{{ $user->name }}</p>
                            <p class="text-xs text-slate-500">{{ $user->email }}</p>
                        </div>
                    </button>
                @empty
                    <div class="p-6 text-center text-sm text-slate-500">
                        No users found
                    </div>
                @endforelse
            </div>
        </div>

        <x-slot:footer>
            <x-ui.button variant="secondary" @click="show = false">Cancel</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>
