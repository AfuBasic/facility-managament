<?php

namespace App\Actions\Client\Users;

use App\Mail\PasswordResetTriggered;
use App\Models\ClientMembership;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ResetUserAccount
{
    public function execute(ClientMembership $membership): void
    {
        // 1. Reset Password to Random
        $membership->user->update([
            'password' => bcrypt(Str::random(32))
        ]);

        // 2. Set Status
        $membership->update([
            'status' => ClientMembership::STATUS_RESET
        ]);

        // 3. Generate Link
        $url = URL::temporarySignedRoute(
            'invitations.accept',
            now()->addHour(),
            ['membership' => $membership->id]
        );

        // 4. Send Email
        Mail::to($membership->user->email)->send(new PasswordResetTriggered($url, $membership->user));
    }
}
