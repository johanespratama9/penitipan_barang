<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Consignment;
use App\Models\Consignor;
use App\Services\ConsignmentService;
use Illuminate\Database\Seeder;

class ConsignmentSeeder extends Seeder
{
    public function run(): void
    {
        $consignor = Consignor::where('code', 'PEN-00001')->first() ?? Consignor::first();
        $category = Category::where('slug', 'elektronik')->first() ?? Category::first();

        if ($consignor && $category) {
            $service = app(ConsignmentService::class);

            $existing = Consignment::where('code', 'CSG-00001')->first();
            if (!$existing) {
                $service->createConsignment(
                    $consignor,
                    [
                        'code' => 'CSG-00001',
                        'received_date' => now()->toDateString(),
                        'expiry_date' => now()->addDays(30)->toDateString(),
                        'status' => 'received',
                        'notes' => 'Penitipan 2 unit aksesoris gadget',
                    ],
                    [
                        [
                            'product_name' => 'Powerbank Anker 20000mAh Fast Charging',
                            'category_id' => $category->id,
                            'quantity' => 2,
                            'purchase_price' => 300000,
                            'selling_price' => 450000,
                            'commission_type' => 'percentage',
                            'commission_value' => 20,
                            'consignor_amount' => 360000,
                            'notes' => 'Kondisi baru segel box',
                        ],
                    ]
                );
            }
        }
    }
}
