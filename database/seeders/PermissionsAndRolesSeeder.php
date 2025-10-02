<?php

/**
 * Created by PhpStorm.
 * User: dima23
 * Date: 01.10.25
 * Time: 12:31.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsAndRolesSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'create projects']);
        Permission::create(['name' => 'edit projects']);
        Permission::create(['name' => 'delete projects']);
        Permission::create(['name' => 'view projects']);
        Permission::create(['name' => 'create tasks']);
        Permission::create(['name' => 'edit tasks']);
        Permission::create(['name' => 'delete tasks']);
        Permission::create(['name' => 'view tasks']);

        $adminRole = Role::create(['name' => 'admin']);

        /*
         * For simplicity, we assign all permissions here.
         * Preferably we do this in AppServiceProvider
         * via Gate::before to give all permissions.
         */
        $adminRole->givePermissionTo(Permission::all());

        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo(['view projects', 'view tasks']);
    }
}
