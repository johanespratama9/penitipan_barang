<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Consignor;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $elektronik = Category::firstOrCreate(['name' => 'Elektronik'], ['slug' => 'elektronik']);
        $pakaian = Category::firstOrCreate(['name' => 'Pakaian'], ['slug' => 'pakaian']);

        $consignor1 = Consignor::where('code', 'PEN-00001')->first() ?? Consignor::first();
        $consignor2 = Consignor::where('code', 'PEN-00002')->first() ?? Consignor::first();

        if ($consignor1 && $elektronik) {
            Product::firstOrCreate(
                ['code' => 'PRD-00001'],
                [
                    'category_id' => $elektronik->id,
                    'consignor_id' => $consignor1->id,
                    'barcode' => '899123456001',
                    'name' => 'Headphone Bluetooth Sony WH-1000XM4',
                    'description' => 'Kondisi mulus like new, kelengkapan fullset box dan kabel.',
                    'purchase_price' => 2000000,
                    'selling_price' => 2500000,
                    'commission_type' => 'percentage',
                    'commission_value' => 20, // 20% komisi toko = Rp 500.000, hak penitip = Rp 2.000.000
                    'consignor_price' => 2000000,
                    'stock' => 1,
                    'status' => 'available',
                    'condition' => 'like_new',
                    'received_at' => now(),
                ]
            );
        }

        if ($consignor2 && $pakaian) {
            Product::firstOrCreate(
                ['code' => 'PRD-00002'],
                [
                    'category_id' => $pakaian->id,
                    'consignor_id' => $consignor2->id,
                    'barcode' => '899123456002',
                    'name' => 'Jaket Kulit Asli Vintage Garut',
                    'description' => 'Jaket kulit domba asli ukuran L warna coklat gelap.',
                    'purchase_price' => 700000,
                    'selling_price' => 850000,
                    'commission_type' => 'fixed',
                    'commission_value' => 100000, // Rp 100.000 komisi toko, hak penitip = Rp 750.000
                    'consignor_price' => 750000,
                    'stock' => 1,
                    'status' => 'available',
                    'condition' => 'used',
                    'received_at' => now(),
                ]
            );
        }
    }
}

