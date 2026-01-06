<?php

namespace App\Actions\Client\Users;

use App\Models\ClientMembership;
use Spatie\Permission\Models\Role;

final class UpdateUserRole
{
    public function execute(ClientMembership $membership, string $newRoleName): void
    {
        $clientAccountId = $membership->client_account_id;
        
        // Set the permissions team context
        setPermissionsTeamId($clientAccountId);
        
        // Find the new role
        $newRole = Role::where('name', $newRoleName)
            ->where('client_account_id', $clientAccountId)
            ->firstOrFail();
        
        // Remove all existing roles for this user in this client context
        $membership->user->roles()
            ->where('client_account_id', $clientAccountId)
            ->detach();
        
        // Assign the new role
        $membership->user->assignRole($newRole);
    }
}
