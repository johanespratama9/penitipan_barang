<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            [
                'email' => 'admin@penitipan.test',
            ],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );

        $user->assignRole('admin');
    }
}