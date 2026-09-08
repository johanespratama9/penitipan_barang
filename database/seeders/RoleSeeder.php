<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache roles & permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Super Admin - akses penuh Filament Shield
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // Admin - semua permission
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // Kasir - role tanpa permission khusus (assign manual jika perlu)
        Role::firstOrCreate(['name' => 'kasir', 'guard_name' => 'web']);

        // Penitip - role tanpa permission khusus
        Role::firstOrCreate(['name' => 'penitip', 'guard_name' => 'web']);
    }
}