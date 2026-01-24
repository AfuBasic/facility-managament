<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Notifications | Admin')]
class Notifications extends Component
{
    use WithPagination;

    public function markAsRead(string $notificationId): void
    {
        $notification = Auth::guard('admin')->user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        Auth::guard('admin')->user()->unreadNotifications->markAsRead();
        $this->dispatch('toast', message: 'All notifications marked as read.', type: 'success');
    }

    public function render()
    {
        $notifications = Auth::guard('admin')->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('livewire.admin.notifications', [
            'notifications' => $notifications,
            'unreadCount' => Auth::guard('admin')->user()->unreadNotifications->count(),
        ]);
    }
}
