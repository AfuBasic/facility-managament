<?php

namespace App\Livewire\Admin\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBadge extends Component
{
    public function render()
    {
        $unreadCount = Auth::guard('admin')->user()?->unreadNotifications()->count() ?? 0;

        return view('livewire.admin.components.notification-badge', [
            'unreadCount' => $unreadCount,
        ]);
    }
}
