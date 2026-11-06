<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat role dapur
        $role = Role::firstOrCreate(['name' => 'dapur', 'guard_name' => 'web']);

        // 2. Buat akun default staf dapur
        $dapurUser = User::firstOrCreate(
            ['email' => 'dapur@penitipan.test'],
            [
                'name'     => 'Staf Dapur',
                'password' => Hash::make('password'),
            ]
        );

        $dapurUser->syncRoles(['dapur']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $dapurUser = User::where('email', 'dapur@penitipan.test')->first();
        if ($dapurUser) {
            $dapurUser->delete();
        }

        $role = Role::where('name', 'dapur')->first();
        if ($role) {
            $role->delete();
        }
    }
};

