<?php

namespace App\Actions\Client\Users;

use App\Events\UserInvited;
use App\Mail\UserInvitation;
use App\Models\ClientAccount;
use App\Models\ClientMembership;
use App\Models\User;
use App\Services\InvitationTracker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class InviteUser
{
    public function execute(string $email, string $roleName, string $clientAccountId): void
    {
        DB::transaction(function() use ($email, $roleName, $clientAccountId) {
            // Check if user already exists
            $existingUser = User::where('email', $email)->first();
            $isNewUser = !$existingUser;
            
            //  Find or Create User
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'New User',
                    'password' => bcrypt(Str::random(32)), // Random password as field is not nullable
                    ]
                );
                
                // Create/Update Membership
                $membership = ClientMembership::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'client_account_id' => $clientAccountId,
                    ],
                    [
                        'status' => ClientMembership::STATUS_PENDING,
                    ]
                );
                setPermissionsTeamId($clientAccountId);
                // Assign Role (Scoped)
                // Ensure role exists and assign. Scope is handled by setPermissionsTeamId middleware/logic
                $user->assignRole($roleName);
                    
                // Generate Signed URL
                $url = URL::temporarySignedRoute(
                    'invitations.accept',
                    now()->addHour(),
                    ['membership' => $membership->id]
                );
                
                // Track invitation
                app(InvitationTracker::class)->recordInvitation(
                    email: $email,
                    roleName: $roleName,
                    clientAccountId: (int) $clientAccountId,
                    invitedByUserId: Auth::id(),
                    isNewUser: $isNewUser
                );
                    
                // Dispatch Event
                $clientAccount = ClientAccount::find($clientAccountId);
                UserInvited::dispatch($user, $url, $clientAccount, $roleName);
        });
    }
}