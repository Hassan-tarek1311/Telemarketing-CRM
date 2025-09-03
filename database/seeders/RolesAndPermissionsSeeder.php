<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Delete the old one in case of rerun
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // premissions
        $permissions = [
            'create articles',
            'edit articles',
            'delete articles',
            'publish articles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $writerRole = Role::firstOrCreate(['name' => 'writer']);

        // Bind permissions to the role
        $adminRole->givePermissionTo(Permission::all());
        $writerRole->givePermissionTo(['create articles', 'edit articles']);

        // Trial admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password')
            ]
        );

        // Link the user to the admin role
        $user->assignRole($adminRole);
    }
}
