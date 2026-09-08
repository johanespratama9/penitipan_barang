<?php

namespace Database\Seeders;

use App\Models\Consignor;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConsignorSeeder extends Seeder
{
    public function run(): void
    {
        $penitipUser = User::where('email', 'penitip@penitipan.test')->first();

        // Penitip 1 - Terhubung dengan akun login
        Consignor::firstOrCreate(
            ['code' => 'PEN-00001'],
            [
                'user_id' => $penitipUser?->id,
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'email' => 'penitip@penitipan.test',
                'address' => 'Jl. Merdeka No. 45, Jakarta',
                'identity_number' => '3171012345670001',
                'status' => 'active',
                'notes' => 'Penitip barang elektronik dan aksesoris',
            ]
        );

        // Penitip 2 - Tanpa akun login (penitip offline)
        Consignor::firstOrCreate(
            ['code' => 'PEN-00002'],
            [
                'user_id' => null,
                'name' => 'Siti Rahmawati',
                'phone' => '085678901234',
                'email' => 'siti@example.com',
                'address' => 'Jl. Sudirman No. 12, Bandung',
                'identity_number' => '3273019876540002',
                'status' => 'active',
                'notes' => 'Penitip pakaian & tas',
            ]
        );
    }
}

