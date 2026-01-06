<?php

namespace App\Actions\Client\Roles;

use Illuminate\Support\Facades\Artisan;

class SeedPermissions
{
    public function execute()
    {
        Artisan::call('db:seed', [
            '--class' => 'PermissionSeeder',
            '--force' => true
        ]);
    }
}
