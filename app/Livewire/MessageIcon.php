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
     * The client account ID for channel scoping.
     */
    public ?int $clientId = null;

    /**
     * The conversation currently being viewed by the user.
     * If set, we won't update badge for messages in this conversation.
     */
    public ?int $focusedConversationId = null;

    public function mount(): void
    {
        // Store client ID for use in getListeners
        try {
            $this->clientId = app(ClientAccount::class)->id;
        } catch (\Exception $e) {
            $this->clientId = session('current_client_account_id');
        }

        $this->refreshUnreadCount();
    }

    /**
     * Get the event listeners for this component.
     *
     * We use a global Livewire event dispatched from JavaScript
     * to handle incoming messages more reliably across SPA navigation.
     */
    public function getListeners(): array
    {
        return [
            'message-received' => 'handleNewMessage',
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
     * Handle incoming message from WebSocket (via global JS handler).
     *
     * @param  array  $event  The message event data (wrapped in 'event' key from JS dispatch)
     */
    #[On('message-received')]
    public function handleNewMessage(array $event): void
    {
        // If user is viewing this conversation, don't update the badge
        if ($this->focusedConversationId === ($event['conversation_id'] ?? null)) {
            return;
        }

        // Increment the badge count
        $this->unreadCount++;

        // Show toast notification for new message
        $senderName = $event['sender_name'] ?? 'Someone';
        $body = $event['body'] ?? 'sent you a message';

        $this->dispatch(
            'toast',
            message: "{$senderName}: ".\Illuminate\Support\Str::limit($body, 50),
            type: 'info',
            position: 'bottom'
        );
    }

    public function refreshUnreadCount(): void
    {
        $userId = Auth::id();

        // Use stored client ID
        if (! $this->clientId) {
            $this->unreadCount = 0;

            return;
        }

        $this->unreadCount = Conversation::where('client_account_id', $this->clientId)
            ->forUser($userId)
            ->get()
            ->sum(fn ($c) => $c->unreadCountFor($userId));
    }

    public function render()
    {
        return view('livewire.message-icon');
    }
}
