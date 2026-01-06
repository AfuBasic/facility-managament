<?php

namespace App\Actions\Client\Users;

use App\Models\ClientMembership;
use Illuminate\Support\Facades\URL;

class ResendInvitation
{
    public function execute(ClientMembership $membership): void
    {
        // Guard: Don't resend if already pending 
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

        \App\Events\InvitationResent::dispatch($membership->user, $url, $membership->clientAccount);
    }
}
