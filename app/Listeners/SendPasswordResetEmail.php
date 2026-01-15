<?php

namespace App\Listeners;

use App\Events\ForgotPasswordRequested;
use App\Mail\PasswordResetEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class SendPasswordResetEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ForgotPasswordRequested $event): void
    {
        $token = Password::createToken($event->user);

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $event->user->email]);

        Mail::to($event->user->email)->queue(new PasswordResetEmail($event->user, $resetUrl));
    }
}
