<?php

namespace App\Actions;

use App\Events\ClientRegistered;
use App\Models\ClientAccount;
use App\Models\ClientMembership;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class PublicUserSignup
{
    public function execute(
        string $organization_name,
        string $email,
        string $password,
        ): User
        {
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
                $user->verification_sent_at = now();
                $user->save();  
                event(new ClientRegistered($user));
                return $user;
            });
            
            return $user;
        }
    }