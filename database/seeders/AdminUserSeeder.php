<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@penitipan.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles(['admin']);

        // Kasir User
        $kasir = User::firstOrCreate(
            ['email' => 'kasir@penitipan.test'],
            [
                'name' => 'Kasir Toko',
                'password' => Hash::make('password'),
            ]
        );
        $kasir->syncRoles(['kasir']);

        // Penitip User
        $penitip = User::firstOrCreate(
            ['email' => 'penitip@penitipan.test'],
            [
                'name' => 'Penitip Barang',
                'password' => Hash::make('password'),
            ]
        );
        $penitip->syncRoles(['penitip']);
    }
}