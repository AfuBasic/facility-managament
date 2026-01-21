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
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
#[Title('Messages | Optima FM')]
class MessagesIndex extends Component
{
    /**
     * Search query for filtering conversations in the sidebar.
     */
    public $search = '';

    /**
     * Controls visibility of the "New Message" modal.
     */
    public $showNewMessageModal = false;

    /**
     * Search query for finding users in the "New Message" modal.
     */
    public $userSearch = '';

    /**
     * The ID of the currently selected/active conversation.
     * Null if no conversation is selected.
     */
    public ?int $activeConversationId = null;

    /**
     * The message text being composed by the user.
     */
    public string $newMessage = '';

    /**
     * The current client account context.
     */
    public ClientAccount $clientAccount;

    /**
     * Initialize the component.
     *
     * Called when the component is first loaded. If a conversation hashid
     * is provided in the URL, it decodes it and sets that conversation as active.
     *
     * @param  string|null  $conversation  The hashid of a conversation from the URL
     */
    public function mount(?string $conversation = null): void
    {
        $this->clientAccount = app(ClientAccount::class);

        if ($conversation) {
            $id = app(HashidService::class)->decode($conversation);
            if ($id) {
                $this->activeConversationId = $id;
                $this->markMessagesAsRead();

                // Tell MessageIcon which conversation we're viewing (so it doesn't show badge for this one)
                $this->dispatch('conversation-focused', conversationId: $id);
            }
        }
    }

    /**
     * Register event listeners.
     *
     * We listen for the global 'message-received' event dispatched from JavaScript.
     * This is more reliable than Livewire's Echo integration with SPA navigation.
     */
    public function getListeners(): array
    {
        return [
            'message-received' => 'handleIncomingMessage',
        ];
    }

    /**
     * Handle an incoming message from WebSocket (via global JS handler).
     *
     * Called when the 'message-received' event is dispatched from JavaScript.
     * - If the message is from us, ignore it (we already have it locally)
     * - If it's for the active conversation, mark it as read and refresh the chat
     * - If it's for a different conversation, just refresh the sidebar (to update unread count)
     *
     * @param  array  $event  The broadcast event data containing message details
     */
    public function handleIncomingMessage(array $event): void
    {
        // Ignore messages we sent (they're already shown locally)
        if (($event['sender_id'] ?? null) === Auth::id()) {
            return;
        }

        // Check if this message belongs to the conversation we're currently viewing
        if ($this->activeConversationId === ($event['conversation_id'] ?? null)) {
            // Mark as read immediately since user is looking at this conversation
            if (isset($event['id'])) {
                Message::where('id', $event['id'])->update(['read_at' => now()]);
            }

            // Clear cached computed properties to force re-fetch
            unset($this->activeMessages, $this->conversations);

            // Tell the browser to scroll the chat to the bottom
            $this->dispatch('scroll-to-bottom');
        } else {
            // Message is for another conversation - just refresh sidebar to show unread badge
            unset($this->conversations);
        }
    }

    /**
     * Get all conversations for the current user.
     *
     * Returns conversations with:
     * - other_user: The other participant in the conversation
     * - unread_count: Number of unread messages from the other user
     * - Sorted by most recent message
     * - Filtered by search query if provided
     *
     * @return \Illuminate\Support\Collection
     */
    #[Computed]
    public function conversations()
    {
        $userId = Auth::id();

        return Conversation::with(['userOne', 'userTwo', 'latestMessage.sender'])
            ->where('client_account_id', $this->clientAccount->id)
            ->forUser($userId)
            ->get()
            ->map(function ($conversation) use ($userId) {
                // Add convenience properties for the view
                $conversation->other_user = $conversation->getOtherUser($userId);
                $conversation->unread_count = $conversation->unreadCountFor($userId);

                return $conversation;
            })
            ->sortByDesc(fn ($c) => $c->latestMessage?->created_at)
            ->when($this->search, function ($collection) {
                // Filter by other user's name if search is provided
                return $collection->filter(function ($conversation) {
                    return str_contains(
                        strtolower($conversation->other_user->name),
                        strtolower($this->search)
                    );
                });
            });
    }

    /**
     * Get the currently active conversation.
     *
     * @return \App\Models\Conversation|null
     */
    #[Computed]
    public function activeConversation()
    {
        if (! $this->activeConversationId) {
            return null;
        }

        return Conversation::with(['userOne', 'userTwo'])
            ->find($this->activeConversationId);
    }

    /**
     * Get messages for the active conversation, grouped by date.
     *
     * Returns messages ordered chronologically and grouped by date (Y-m-d)
     * for displaying date dividers in the chat UI.
     *
     * @return \Illuminate\Support\Collection
     */
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

    /**
     * Get the other user in the active conversation.
     *
     * @return \App\Models\User|null
     */
    #[Computed]
    public function otherUser()
    {
        if (! $this->activeConversation) {
            return null;
        }

        return $this->activeConversation->getOtherUser(Auth::id());
    }

    /**
     * Get users available to start a new conversation with.
     *
     * Returns users who are members of the same client account,
     * excluding the current user, filtered by search query.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
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

    /**
     * Select a conversation to view.
     *
     * Decodes the hashid, sets the conversation as active, marks messages as read,
     * and updates the browser URL without a full page reload.
     *
     * @param  string  $hashid  The encoded conversation ID
     */
    public function selectConversation(string $hashid): void
    {
        $id = app(HashidService::class)->decode($hashid);
        if ($id) {
            $this->activeConversationId = $id;
            $this->markMessagesAsRead();

            // Clear cached computed properties
            unset($this->activeConversation, $this->activeMessages, $this->otherUser);

            // Tell MessageIcon which conversation we're viewing
            $this->dispatch('conversation-focused', conversationId: $id);

            // Update browser URL without page reload (handled by Alpine in the view)
            $this->dispatch('url-changed', url: route('app.messages.show', $hashid));
        }
    }

    /**
     * Open the "New Message" modal.
     */
    public function openNewMessage(): void
    {
        $this->userSearch = '';
        $this->showNewMessageModal = true;
    }

    /**
     * Start a conversation with a user.
     *
     * Creates a new conversation if one doesn't exist between the two users,
     * or opens the existing one. Conversations are stored with user IDs in
     * ascending order (user_one_id < user_two_id) to ensure uniqueness.
     *
     * @param  int  $userId  The ID of the user to start a conversation with
     */
    public function startConversation(int $userId): void
    {
        $currentUserId = Auth::id();

        // Check if conversation already exists between these two users
        $conversation = Conversation::where('client_account_id', $this->clientAccount->id)
            ->betweenUsers($currentUserId, $userId)
            ->first();

        if (! $conversation) {
            // Create new conversation with IDs in ascending order for uniqueness
            $conversation = Conversation::create([
                'client_account_id' => $this->clientAccount->id,
                'user_one_id' => min($currentUserId, $userId),
                'user_two_id' => max($currentUserId, $userId),
            ]);
        }

        $this->activeConversationId = $conversation->id;
        $this->showNewMessageModal = false;

        // Clear all cached computed properties
        unset($this->conversations, $this->activeConversation, $this->activeMessages, $this->otherUser);

        // Notify other components and update URL
        $this->dispatch('conversation-focused', conversationId: $conversation->id);
        $this->dispatch('url-changed', url: route('app.messages.show', $conversation->hashid));
    }

    /**
     * Send a message in the active conversation.
     *
     * Validates the message, creates it in the database, updates the conversation
     * timestamp, and broadcasts it via WebSocket to the other user.
     */
    public function sendMessage(): void
    {
        if (! $this->activeConversationId || ! $this->activeConversation) {
            return;
        }

        $this->validate([
            'newMessage' => 'required|string|max:5000',
        ]);

        // Create the message
        $message = Message::create([
            'conversation_id' => $this->activeConversationId,
            'sender_id' => Auth::id(),
            'body' => $this->newMessage,
        ]);

        // Update conversation's updated_at timestamp (for sorting)
        $this->activeConversation->touch();

        // Get the recipient (the other user in this conversation)
        $recipientId = $this->activeConversation->getOtherUser(Auth::id())->id;

        // Broadcast the message via Reverb WebSocket
        // This will be received by the recipient's MessageIcon and MessagesIndex components
        broadcast(new MessageSent($message->load(['sender', 'conversation']), $recipientId));

        // Clear input and refresh the view
        $this->newMessage = '';
        unset($this->activeMessages, $this->conversations);

        // Scroll chat to bottom to show new message
        $this->dispatch('scroll-to-bottom');
    }

    /**
     * Mark all messages in the active conversation as read.
     *
     * Only marks messages from the other user (not our own messages).
     */
    public function markMessagesAsRead(): void
    {
        if (! $this->activeConversationId) {
            return;
        }

        Message::where('conversation_id', $this->activeConversationId)
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Refresh sidebar to update unread counts
        unset($this->conversations);
    }

    public function render()
    {
        return view('livewire.client.messages-index');
    }
}
