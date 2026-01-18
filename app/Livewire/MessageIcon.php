<?php

namespace App\Livewire;

use App\Models\ClientAccount;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MessageIcon extends Component
{
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->refreshUnreadCount();
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
