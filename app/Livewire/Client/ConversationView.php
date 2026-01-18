<?php

namespace App\Livewire\Client;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.client-app')]
#[Title('Conversation | Optima FM')]
class ConversationView extends Component
{
    public int $conversationId;

    public string $newMessage = '';

    public function mount(Conversation $conversation): void
    {
        $this->conversationId = $conversation->id;

        // Mark all unread messages as read
        $this->markMessagesAsRead();
    }

    #[Computed]
    public function conversation(): Conversation
    {
        return Conversation::with(['userOne', 'userTwo'])->findOrFail($this->conversationId);
    }

    #[Computed]
    public function messages()
    {
        return Message::where('conversation_id', $this->conversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy(fn ($m) => $m->created_at->format('Y-m-d'));
    }

    #[Computed]
    public function otherUser()
    {
        return $this->conversation->getOtherUser(Auth::id());
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => 'required|string|max:5000',
        ]);

        Message::create([
            'conversation_id' => $this->conversationId,
            'sender_id' => Auth::id(),
            'body' => $this->newMessage,
        ]);

        // Update conversation timestamp
        $this->conversation->touch();

        $this->newMessage = '';

        // Clear computed cache
        unset($this->messages);
    }

    public function markMessagesAsRead(): void
    {
        Message::where('conversation_id', $this->conversationId)
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.client.conversation-view', [
            'messages' => $this->messages,
            'otherUser' => $this->otherUser,
        ]);
    }
}
