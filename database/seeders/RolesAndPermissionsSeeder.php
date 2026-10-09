<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'manage products',
            'manage categories',
            'manage media',
            'manage pages',
            'manage customers',
            'manage orders',
            'view stats',
            'manage users',
            'manage settings',
            'moderate reviews',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        $designer = Role::firstOrCreate(['name' => 'designer', 'guard_name' => 'web']);
        $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $admin    = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $designer->syncPermissions(['manage media', 'manage pages', 'manage products', 'manage categories']);
        $employee->syncPermissions(['manage products', 'manage categories', 'manage customers', 'manage orders', 'view stats', 'moderate reviews']);
        $admin->syncPermissions(Permission::all());

        // customer typicky nemá nič (alebo len view vlastných vecí v app)
        $customer->syncPermissions([]);
    }
}
