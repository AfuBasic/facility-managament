<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public function mount()
    {
        $this->refreshUnreadCount();
    }

    public function getListeners(): array
    {
        $userId = Auth::id();

        return [
            "echo-private:user.{$userId},.notification.received" => 'handleNewNotification',
        ];
    }

    public function handleNewNotification(array $payload): void
    {
        $this->refreshUnreadCount();
        $this->dispatch('notification-received', notification: $payload['notification'] ?? []);
    }

    public function refreshUnreadCount(): void
    {
        $this->unreadCount = Auth::user()->unreadNotifications()->count();
    }

    public function getNotificationsProperty()
    {
        return Auth::user()
            ->unreadNotifications()
            ->latest()
            ->take(10)
            ->get();
    }

    public function markAsRead(string $notificationId, bool $redirect = false): void
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            $this->refreshUnreadCount();

            if ($redirect && isset($notification->data['route'])) {
                $this->redirect($notification->data['route']);
            }
        }
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->refreshUnreadCount();
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'notifications' => $this->notifications,
        ]);
    }
}
