<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Create permissions
        $permissions = [
            // User management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Products management
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',

            // API specific permissions
            'api.admin',
            'api.user',
        ];

        foreach ($permissions as $permission){
            Permission::create(['name' => $permission]);
        }

        // Assigns permissions to roles
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole->givePermissionTo([
            'users.view',
            'users.create',
            'users.edit',
            'products.view',
            'products.create',
            'products.delete',
            'products.edit',
            'api.admin',
        ]);

        $userRole->givePermissionTo([
            'users.create',
            'users.edit',
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            'api.user',
        ]);
    }
}
