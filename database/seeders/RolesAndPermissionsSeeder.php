<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

        $role = fn (UserRole $role) => Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);

        $role(UserRole::Designer)->syncPermissions(['manage media', 'manage pages', 'manage products', 'manage categories']);
        $role(UserRole::Employee)->syncPermissions(['manage products', 'manage categories', 'manage customers', 'manage orders', 'view stats', 'moderate reviews']);

        // Admin nema v kode ziadnu vynimku (Gate::before), pristup ma vdaka vsetkym opravneniam.
        // Po pridani noveho opravnenia treba seeder spustit znova.
        $role(UserRole::Admin)->syncPermissions(Permission::all());

        // Zakaznik nema ziadne opravnenie do administracie
        $role(UserRole::Customer)->syncPermissions([]);
    }
}
