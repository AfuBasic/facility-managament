<?php

namespace App\Actions\Client\Users;

use App\Mail\UserInvitation;
use App\Models\ClientMembership;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class ResendInvitation
{
    public function execute(ClientMembership $membership): void
    {
        // Guard: Don't resend if already pending (Strict adherence to user request)
        if ($membership->status === ClientMembership::STATUS_PENDING) {
             return; 
        }

        // Update status back to pending
        $membership->update(['status' => ClientMembership::STATUS_PENDING]);
        
        $url = URL::temporarySignedRoute(
            'invitations.accept',
            now()->addHour(),
            ['membership' => $membership->id]
        );

        Mail::to($membership->user->email)->send(new UserInvitation($url, $membership->user));
    }
}
