<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Facilities
            'view facilities',
            'create facilities',
            'edit facilities',
            'delete facilities',
            'manage facilities',

            //Spaces
            'view spaces',
            'create spaces',
            'edit spaces',
            'delete spaces',

            //Stores
            'view stores',
            'create stores',
            'edit stores',
            'delete stores',
            'manage stores',

            //Consumables
            'view consumables',
            'create consumables',
            'edit consumables',
            'delete consumables',

            //Facility Managers
            'view facility_managers',
            'assign facility_managers',
            'unassign facility_managers',
            'delete facility_managers',

            // Assets
            'view assets',
            'create assets',
            'edit assets',
            'delete assets',

            // Work Orders
            'view work orders',
            'create work orders',
            'edit work orders',
            'delete work orders',

            // Vendors
            'view vendors',
            'create vendors',
            'edit vendors',
            'delete vendors',

            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Roles
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // SLA Policy
            'view sla policy',
            'edit sla policy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
