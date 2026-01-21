<div x-data="{ isAnimating: false }">
    {{-- AI Assistant Floating Widget --}}
    
    {{-- Floating Button with subtle pulse animation --}}
    @if(!$isOpen)
        <div class="fixed bottom-6 right-6 z-50">
            <div class="relative">
                {{-- Subtle pulse ring - slower and less intense --}}
                <div class="absolute inset-0 rounded-full bg-blue-400 opacity-40 animate-pulse"></div>
                
                <flux:button 
                    wire:click="toggleChat" 
                    variant="primary" 
                    class="relative rounded-full shadow-lg hover:shadow-2xl hover:scale-105 transition-all duration-300 px-3 py-3 sm:px-6"
                    icon="sparkles"
                >
                    <span class="font-semibold hidden sm:inline">AI Assistant</span>
                </flux:button>
            </div>
        </div>
    @endif

    {{-- Chat Window with slide-up animation --}}
    @if($isOpen)
        <div 
            class="fixed bottom-6 right-6 z-50 w-96 h-[600px] flex flex-col bg-white dark:bg-zinc-900 rounded-lg shadow-2xl border border-zinc-200 dark:border-zinc-800"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        >
            
            {{-- Header with gradient and status --}}
            <div class="flex items-center justify-between p-4 border-b border-zinc-200 dark:border-zinc-800 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-t-lg">
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <flux:icon.sparkles class="w-5 h-5 text-white" />
                        @if($isProcessing)
                            <div class="absolute -top-1 -right-1 w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        @endif
                    </div>
                    <flux:heading size="sm" class="text-white font-bold">AI Assistant</flux:heading>
                    @if($isProcessing)
                        <span class="text-xs text-white/90 animate-pulse">● Thinking...</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <flux:button 
                        wire:click="clearConversation" 
                        variant="ghost" 
                        size="sm"
                        icon="trash"
                        class="text-white hover:bg-white/20 [&_svg]:text-white transition-all hover:scale-110"
                    />
                    <flux:button 
                        wire:click="toggleChat" 
                        variant="ghost" 
                        size="sm"
                        icon="x-mark"
                        class="text-white hover:bg-white/20 [&_svg]:text-white transition-all hover:scale-110"
                    />
                </div>
            </div>

            {{-- Messages Container with custom scrollbar --}}
            <div 
                class="flex-1 overflow-y-auto p-4 space-y-4 scroll-smooth" 
                id="chat-messages"
                style="scrollbar-width: thin; scrollbar-color: rgb(161 161 170) transparent;"
            >
                @foreach($conversation as $index => $msg)
                    <div 
                        class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }} message-bubble"
                        style="animation-delay: {{ $index * 0.05 }}s"
                    >
                        <div class="max-w-[80%]">
                            {{-- Message Bubble with hover effect --}}
                            <div class="
                                {{ $msg['role'] === 'user' 
                                    ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-l-2xl rounded-tr-2xl' 
                                    : 'bg-gradient-to-br from-zinc-100 to-zinc-50 dark:from-zinc-800 dark:to-zinc-900 text-zinc-900 dark:text-zinc-100 rounded-r-2xl rounded-tl-2xl' 
                                }}
                                px-4 py-3 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5
                            ">
                                <p class="text-sm whitespace-pre-wrap leading-relaxed">{{ $msg['content'] }}</p>
                            </div>

                            {{-- Links with animation --}}
                            @if(isset($msg['links']) && count($msg['links']) > 0)
                                <div class="mt-2 space-y-1">
                                    @foreach($msg['links'] as $link)
                                        <a 
                                            href="{{ $link['url'] }}" 
                                            class="inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 hover:underline transition-all hover:translate-x-1 link-item"
                                        >
                                            <flux:icon.arrow-right class="w-3 h-3" />
                                            {{ $link['text'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Timestamp --}}
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 opacity-70">
                                {{ \Carbon\Carbon::parse($msg['timestamp'])->format('g:i A') }}
                            </p>
                        </div>
                    </div>
                @endforeach

                {{-- Enhanced Typing Indicator --}}
                @if($isProcessing)
                    <div class="flex justify-start typing-indicator">
                        <div class="bg-gradient-to-br from-zinc-100 to-zinc-50 dark:from-zinc-800 dark:to-zinc-900 rounded-r-2xl rounded-tl-2xl px-5 py-4 shadow-md">
                            <div class="flex gap-1.5">
                                <div class="w-2.5 h-2.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0ms; animation-duration: 1s;"></div>
                                <div class="w-2.5 h-2.5 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 200ms; animation-duration: 1s;"></div>
                                <div class="w-2.5 h-2.5 bg-pink-500 rounded-full animate-bounce" style="animation-delay: 400ms; animation-duration: 1s;"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Input Area with better styling --}}
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 rounded-b-lg">
                <form wire:submit.prevent="sendMessage" class="flex gap-2">
                    <div class="flex-1 relative">
                        <flux:input 
                            wire:model="message" 
                            placeholder="Ask me anything..."
                            class="w-full pr-10 transition-all focus:ring-2 focus:ring-blue-500 focus:scale-[1.02]"
                            :disabled="$isProcessing"
                            autofocus
                        />
                        @if($isProcessing)
                            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                <div class="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                            </div>
                        @endif
                    </div>
                    <flux:button 
                        type="submit" 
                        variant="primary"
                        icon="paper-airplane"
                        :disabled="$isProcessing"
                        class="transition-all hover:scale-110 active:scale-95 shadow-md hover:shadow-lg"
                    />
                </form>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-2 flex items-center gap-1">
                    <flux:icon.light-bulb class="w-3 h-3 animate-pulse" />
                    <span>Try: "How many open work orders do we have?"</span>
                </p>
            </div>
        </div>
    @endif

    {{-- Enhanced auto-scroll with smooth behavior --}}
    <script>
        document.addEventListener('livewire:init', () => {
            // Scroll to bottom on initial load
            const scrollToBottom = () => {
                const chatMessages = document.getElementById('chat-messages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            };

            // Scroll on mount
            setTimeout(scrollToBottom, 100);

            // Scroll after Livewire updates
            Livewire.hook('message.processed', (message, component) => {
                if (component.name === 'ai-assistant') {
                    setTimeout(() => {
                        const chatMessages = document.getElementById('chat-messages');
                        if (chatMessages) {
                            chatMessages.scrollTo({
                                top: chatMessages.scrollHeight,
                                behavior: 'smooth'
                            });
                        }
                    }, 150);
                }
            });
        });
    </script>

    {{-- Custom animations --}}
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .message-bubble {
            animation: fadeInUp 0.3s ease-out forwards;
            opacity: 0;
        }

        .typing-indicator {
            animation: fadeIn 0.3s ease-out;
        }

        .link-item {
            animation: fadeInUp 0.2s ease-out forwards;
            opacity: 0;
        }

        /* Custom scrollbar for webkit browsers */
        #chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        #chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        #chat-messages::-webkit-scrollbar-thumb {
            background: rgb(161 161 170);
            border-radius: 3px;
        }

        #chat-messages::-webkit-scrollbar-thumb:hover {
            background: rgb(113 113 122);
        }

        /* Smooth transitions for all interactive elements */
        * {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</div>
