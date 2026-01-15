<?php

namespace App\Listeners;

use App\Events\InvitationResent;
use App\Events\UserInvited;
use App\Mail\UserInvitation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendUserInvitation implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UserInvited|InvitationResent $event): void
    {
        Mail::to($event->user->email)->send(new UserInvitation($event->url, $event->user, $event->clientAccount, $event->role));
    }
}
