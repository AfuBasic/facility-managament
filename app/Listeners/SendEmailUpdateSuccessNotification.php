<?php

namespace App\Listeners;

use App\Events\EmailUpdated;
use App\Mail\EmailUpdateSuccess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailUpdateSuccessNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(EmailUpdated $event): void
    {
        // Send to the OLD email address
        Mail::to($event->oldEmail)->send(new EmailUpdateSuccess($event->user, $event->newEmail));
    }
}
