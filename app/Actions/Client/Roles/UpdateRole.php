<?php

namespace App\Actions\Client\Roles;

use Spatie\Permission\Models\Role;

class UpdateRole
{
    public function execute(Role $role, string $name, array $permissions): Role
    {
        $role->update(['name' => $name]);
        $role->syncPermissions($permissions);

        return $role;
    }
}
