<?php

namespace App\Listeners;

use App\Events\EmailUpdateOtpRequested;
use App\Mail\EmailUpdateOtp;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailUpdateOtp implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmailUpdateOtpRequested $event): void
    {
        Mail::to($event->user->email)->send(new EmailUpdateOtp($event->otp));
    }
}
