<?php

namespace App\Livewire;

use App\Models\ClientAccount;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class MessageIcon extends Component
{
    public int $unreadCount = 0;

    /**
     * The conversation currently being viewed by the user.
     * If set, we won't update badge for messages in this conversation.
     */
    public ?int $focusedConversationId = null;

    public function mount(): void
    {
        $this->refreshUnreadCount();
    }

    /**
     * Get the Echo listeners for real-time updates.
     */
    public function getListeners(): array
    {
        $userId = Auth::id();

        return [
            "echo-private:user.{$userId},.message.sent" => 'handleNewMessage',
            'conversation-focused' => 'setFocusedConversation',
        ];
    }

    /**
     * Track which conversation the user is currently viewing.
     */
    #[On('conversation-focused')]
    public function setFocusedConversation(?int $conversationId): void
    {
        $this->focusedConversationId = $conversationId;
    }

    /**
     * Handle incoming message from WebSocket.
     */
    public function handleNewMessage(array $event): void
    {
        // If user is viewing this conversation, don't update the badge
        // (the message is already being marked as read in MessagesIndex)
        if ($this->focusedConversationId === $event['conversation_id']) {
            return;
        }

        // Increment the badge count
        $this->unreadCount++;
    }

    public function refreshUnreadCount(): void
    {
        $userId = Auth::id();

        // Check if we have a client context
        try {
            $clientId = app(ClientAccount::class)->id;
        } catch (\Exception $e) {
            $this->unreadCount = 0;

            return;
        }

        $this->unreadCount = Conversation::where('client_account_id', $clientId)
            ->forUser($userId)
            ->get()
            ->sum(fn ($c) => $c->unreadCountFor($userId));
    }

    public function render()
    {
        return view('livewire.message-icon');
    }
}
