<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $conversationId;

    public int $senderId;

    public int $recipientId;

    public int $clientAccountId;

    public string $senderName;

    public string $body;

    public string $messageId;

    public string $createdAt;

    public function __construct(Message $message, int $recipientId)
    {
        // Store primitive values to avoid serialization issues
        $this->conversationId = $message->conversation_id;
        $this->senderId = $message->sender_id;
        $this->recipientId = $recipientId;
        $this->clientAccountId = $message->conversation->client_account_id;
        $this->senderName = $message->sender->name;
        $this->body = $message->body;
        $this->messageId = (string) $message->id;
        $this->createdAt = $message->created_at->toIso8601String();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * We broadcast to a client-scoped user channel to ensure users only
     * receive notifications for the specific client account context.
     */
    public function broadcastOn(): array
    {
        return [
            // Broadcast to the conversation channel (for real-time chat)
            new PrivateChannel('conversation.'.$this->conversationId),

            // Broadcast to recipient's client-scoped channel (for badge updates)
            // This ensures users only get notifications for their current client context
            new PrivateChannel('client.'.$this->clientAccountId.'.user.'.$this->recipientId),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->messageId,
            'conversation_id' => $this->conversationId,
            'sender_id' => $this->senderId,
            'sender_name' => $this->senderName,
            'body' => $this->body,
            'created_at' => $this->createdAt,
            'client_account_id' => $this->clientAccountId,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
