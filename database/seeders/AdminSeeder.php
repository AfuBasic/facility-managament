<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'support@optimafm.org'],
            [
                'name' => 'System Admin',
                'password' => Hash::make(config('services.admin.password')),
            ]
        );
    }
}
