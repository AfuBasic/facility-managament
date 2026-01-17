<?php

namespace App\Livewire\Client;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.client-app')]
class Notifications extends Component
{
    use WithPagination;

    #[Url]
    public string $filter = 'all';

    public function markAsRead(string $notificationId, bool $redirect = false): void
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();

            if ($redirect && isset($notification->data['route'])) {
                $this->redirect($notification->data['route']);
            }
        }
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function deleteNotification(string $notificationId): void
    {
        Auth::user()
            ->notifications()
            ->where('id', $notificationId)
            ->delete();
    }

    public function deleteAllRead(): void
    {
        Auth::user()
            ->notifications()
            ->whereNotNull('read_at')
            ->delete();
    }

    public function render()
    {
        $query = Auth::user()->notifications();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        return view('livewire.client.notifications', [
            'notifications' => $query->latest()->paginate(15),
            'unreadCount' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
}
