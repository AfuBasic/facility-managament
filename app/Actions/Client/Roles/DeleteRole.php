<?php

namespace App\Actions\Client;

use Spatie\Permission\Models\Role;

class DeleteRole
{
    public function execute(Role $role): void
    {
        $role->delete();
    }
}
