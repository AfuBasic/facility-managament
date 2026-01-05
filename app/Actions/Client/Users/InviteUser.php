<?php

namespace App\Actions\Client\Users;

use App\Mail\UserInvitation;
use App\Models\ClientMembership;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class InviteUser
{
    public function execute(string $email, string $roleName, string $clientAccountId): void
    {
        // 1. Find or Create User
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'New User',
                'password' => bcrypt(Str::random(32)), // Random password as field is not nullable
            ]
        );

        // 2. Create/Update Membership
        $membership = ClientMembership::updateOrCreate(
            [
                'user_id' => $user->id,
                'client_account_id' => $clientAccountId,
            ],
            [
                'status' => ClientMembership::STATUS_PENDING,
            ]
        );

        // 3. Assign Role (Scoped)
        // Ensure role exists and assign. Scope is handled by setPermissionsTeamId middleware/logic
        $user->assignRole($roleName);

        // 4. Generate Signed URL
        $url = URL::temporarySignedRoute(
            'invitations.accept',
            now()->addHour(),
            ['membership' => $membership->id]
        );

        // 5. Send Email
        Mail::to($user->email)->send(new UserInvitation($url, $user));
    }
}
