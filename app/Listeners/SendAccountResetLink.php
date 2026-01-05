<?php

namespace App\Listeners;

use App\Events\AccountResetTriggered;
use App\Mail\PasswordResetTriggered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendAccountResetLink implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(AccountResetTriggered $event): void
    {
        Mail::to($event->user->email)->send(new PasswordResetTriggered($event->url, $event->user));
    }
}
