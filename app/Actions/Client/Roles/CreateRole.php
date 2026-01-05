<?php

namespace App\Actions\Client;

use App\Models\ClientAccount;
use Spatie\Permission\Models\Role;

class CreateRole
{
    public function execute(string $name, array $permissions, string $clientAccountId): Role
    {
        $role = Role::create([
            'name' => $name,
            'guard_name' => 'web',
            'client_account_id' => $clientAccountId
        ]);

        $role->syncPermissions($permissions);

        return $role;
    }
}
