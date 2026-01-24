<?php

namespace App\Actions;

use App\Events\ClientRegistered;
use App\Models\Admin;
use App\Models\ClientAccount;
use App\Models\ClientMembership;
use App\Models\User;
use App\Notifications\AdminNewUserNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

final class PublicUserSignup
{
    public function execute(
        string $organization_name,
        string $email,
        string $password,
    ): User {
        $user = DB::transaction(function () use ($organization_name, $email, $password) {
            /**
             * Create the user
             */
            $user = User::create([
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            /**
             * Create the Client
             */
            $client = ClientAccount::create([
                'name' => $organization_name,
            ]);

            /**
             * Attach the user to the client and mark invitation as accepted
             */
            $client->memberships()->create([
                'user_id' => $user->id,
                'status' => ClientMembership::STATUS_ACCEPTED,
            ]);

            // Set the current team id to the new client's id so the role is created for this team
            setPermissionsTeamId($client->id);

            // Create the admin role for this client
            $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'client_account_id' => $client->id]);

            // Assign the role to the user (team context is already set via setPermissionsTeamId)
            $user->assignRole($role);

            $user->verification_sent_at = now();
            $user->save();
            event(new ClientRegistered($user));

            // Notify all admins about the new user registration
            Notification::send(Admin::all(), new AdminNewUserNotification($user, 'email'));

            return $user;
        });

        return $user;
    }
}
