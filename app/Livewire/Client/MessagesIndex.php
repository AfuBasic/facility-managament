<?php

namespace App\Livewire\Client;

use App\Events\MessageSent;
use App\Models\ClientAccount;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\HashidService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
#[Title('Messages | Optima FM')]
class MessagesIndex extends Component
{
    public $search = '';

    public $showNewMessageModal = false;

    public $userSearch = '';

    public ?int $activeConversationId = null;

    public string $newMessage = '';

    public ClientAccount $clientAccount;

    public function mount(?string $conversation = null): void
    {
        $this->clientAccount = app(ClientAccount::class);

        // If conversation hashid provided, decode and set active
        if ($conversation) {
            $id = app(HashidService::class)->decode($conversation);
            if ($id) {
                $this->activeConversationId = $id;
                $this->markMessagesAsRead();

                // Notify other components which conversation is focused (for badge optimization)
                $this->dispatch('conversation-focused', conversationId: $id);
            }
        }
    }

    /**
     * Get the Echo listeners for real-time updates.
     */
    public function getListeners(): array
    {
        $listeners = [];

        if ($this->activeConversationId) {
            $listeners["echo-private:conversation.{$this->activeConversationId},.message.sent"] = 'handleIncomingMessage';
        }

        return $listeners;
    }

    /**
     * Handle incoming message from WebSocket.
     */
    #[On('echo-private:conversation.{activeConversationId},.message.sent')]
    public function handleIncomingMessage(array $event): void
    {
        // If the message is from us, ignore (we already have it)
        if ($event['sender_id'] === Auth::id()) {
            return;
        }

        // Mark as read immediately since user is viewing this conversation
        Message::where('id', $event['id'])->update(['read_at' => now()]);

        // Refresh messages
        unset($this->activeMessages, $this->conversations);

        // Scroll to bottom
        $this->dispatch('scroll-to-bottom');
    }

    #[Computed]
    public function conversations()
    {
        $userId = Auth::id();

        return Conversation::with(['userOne', 'userTwo', 'latestMessage.sender'])
            ->where('client_account_id', $this->clientAccount->id)
            ->forUser($userId)
            ->get()
            ->map(function ($conversation) use ($userId) {
                $conversation->other_user = $conversation->getOtherUser($userId);
                $conversation->unread_count = $conversation->unreadCountFor($userId);

                return $conversation;
            })
            ->sortByDesc(fn ($c) => $c->latestMessage?->created_at)
            ->when($this->search, function ($collection) {
                return $collection->filter(function ($conversation) {
                    return str_contains(
                        strtolower($conversation->other_user->name),
                        strtolower($this->search)
                    );
                });
            });
    }

    #[Computed]
    public function activeConversation()
    {
        if (! $this->activeConversationId) {
            return null;
        }

        return Conversation::with(['userOne', 'userTwo'])
            ->find($this->activeConversationId);
    }

    #[Computed]
    public function activeMessages()
    {
        if (! $this->activeConversationId) {
            return collect();
        }

        return Message::where('conversation_id', $this->activeConversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(fn ($m) => $m->created_at->format('Y-m-d'));
    }

    #[Computed]
    public function otherUser()
    {
        if (! $this->activeConversation) {
            return null;
        }

        return $this->activeConversation->getOtherUser(Auth::id());
    }

    #[Computed]
    public function availableUsers()
    {
        return User::whereHas('clientMemberships', function ($q) {
            $q->where('client_account_id', $this->clientAccount->id)
                ->where('status', 'accepted');
        })
            ->where('id', '!=', Auth::id())
            ->when($this->userSearch, fn ($query) => $query->where('name', 'like', "%{$this->userSearch}%"))
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    public function selectConversation(string $hashid): void
    {
        $id = app(HashidService::class)->decode($hashid);
        if ($id) {
            $this->activeConversationId = $id;
            $this->markMessagesAsRead();
            unset($this->activeConversation, $this->activeMessages, $this->otherUser);

            // Notify other components which conversation is focused (for badge optimization)
            $this->dispatch('conversation-focused', conversationId: $id);

            // Update URL without full page reload
            $this->dispatch('urlChanged', url: route('app.messages.show', $hashid));
        }
    }

    public function openNewMessage(): void
    {
        $this->userSearch = '';
        $this->showNewMessageModal = true;
    }

    public function startConversation(int $userId): void
    {
        $currentUserId = Auth::id();

        // Check if conversation already exists
        $conversation = Conversation::where('client_account_id', $this->clientAccount->id)
            ->betweenUsers($currentUserId, $userId)
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'client_account_id' => $this->clientAccount->id,
                'user_one_id' => min($currentUserId, $userId),
                'user_two_id' => max($currentUserId, $userId),
            ]);
        }

        $this->activeConversationId = $conversation->id;
        $this->showNewMessageModal = false;
        unset($this->conversations, $this->activeConversation, $this->activeMessages, $this->otherUser);
    }

    public function sendMessage(): void
    {
        if (! $this->activeConversationId) {
            return;
        }

        $this->validate([
            'newMessage' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'conversation_id' => $this->activeConversationId,
            'sender_id' => Auth::id(),
            'body' => $this->newMessage,
        ]);

        // Update conversation timestamp
        $this->activeConversation->touch();

        // Broadcast the message via WebSocket
        broadcast(new MessageSent($message->load(['sender', 'conversation.userOne', 'conversation.userTwo'])));

        $this->newMessage = '';
        unset($this->activeMessages, $this->conversations);
    }

    public function markMessagesAsRead(): void
    {
        if (! $this->activeConversationId) {
            return;
        }

        Message::where('conversation_id', $this->activeConversationId)
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        unset($this->conversations);
    }

    public function refreshMessages(): void
    {
        // Clear cached messages to get fresh data
        unset($this->activeMessages, $this->conversations);
        $this->markMessagesAsRead();

        // Dispatch scroll to bottom event
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        return view('livewire.client.messages-index');
    }
}
