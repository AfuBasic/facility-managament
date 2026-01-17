<?php

namespace App\Listeners;

use App\Events\NewNotificationReceived;
use App\Models\User;
use Illuminate\Notifications\Events\NotificationSent;

class BroadcastNotificationSent
{
    public function handle(NotificationSent $event): void
    {
        // Only broadcast database notifications
        if ($event->channel !== 'database') {
            return;
        }

        // Ensure the notifiable is a User
        if (! $event->notifiable instanceof User) {
            return;
        }

        // Get the notification data
        $notificationData = $event->notification->toArray($event->notifiable);

        // Get the latest notification from the database to include the ID
        $latestNotification = $event->notifiable
            ->notifications()
            ->latest()
            ->first();

        if ($latestNotification) {
            $notificationData['id'] = $latestNotification->id;
            $notificationData['created_at'] = $latestNotification->created_at->toISOString();
        }

        // Broadcast the event
        NewNotificationReceived::dispatch(
            $event->notifiable->id,
            $notificationData
        );
    }
}
