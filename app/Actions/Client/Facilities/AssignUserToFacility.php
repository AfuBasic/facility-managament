<?php

namespace App\Actions\Client\Facilities;

use App\Models\Facility;
use App\Models\User;

class AssignUserToFacility
{
    /**
     * Assign a user to a facility
     */
    public function execute(Facility $facility, int $userId): void
    {
        // Verify user belongs to the same client account
        $user = User::findOrFail($userId);
        
        if (!$user->clientMemberships()->where('client_account_id', $facility->client_account_id)->exists()) {
            throw new \Exception('User does not belong to this client account');
        }

        $facility->assignUser($userId);
    }
}
