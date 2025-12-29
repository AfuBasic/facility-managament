<?php

namespace App\Listeners;

use App\Events\ClientRegistered;
use App\Events\ResendVerification;
use App\Mail\ActivationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendActivationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ClientRegistered|ResendVerification $event): void
    {
        $token = Str::random(64);
        $event->user->activationToken()->create([
            'token' => Hash::make($token),
            'expires_at' => now()->addMinutes(60),
        ]);
                
        $activationUrl = route('activate', ['token' => $token]);
        Mail::to($event->user->email)->queue(new ActivationEmail($event->user, $activationUrl));
    }
}
