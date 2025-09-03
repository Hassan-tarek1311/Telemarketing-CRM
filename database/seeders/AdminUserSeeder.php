<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        //First we need to clean the Spatie cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create a role named admin (if it doesn't exist)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create an admin user (if it doesn't exist)
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // Condition
            [
                'name' => 'Admin',
                'password' => bcrypt('secret123'), // Change the password as you like
            ]
        );

        // Link the user to the admin role
        if (! $user->hasRole('admin')) {
            $user->assignRole($adminRole);
        }
    }
}
