<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\Consignor;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ConsignmentService
{
    /**
     * Buat data consignment baru beserta rincian barangnya.
     */
    public function createConsignment(Consignor $consignor, array $data, array $items): Consignment
    {
        return DB::transaction(function () use ($consignor, $data, $items) {
            $consignment = Consignment::create([
                'consignor_id' => $consignor->id,
                'code' => $data['code'] ?? Consignment::generateUniqueCode(),
                'received_date' => $data['received_date'] ?? now()->toDateString(),
                'expiry_date' => $data['expiry_date'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'notes' => $data['notes'] ?? null,
            ]);

            $commissionService = app(CommissionService::class);
            $totalQty = 0;

            foreach ($items as $itemData) {
                $qty = (int) ($itemData['quantity'] ?? 1);
                $selling = (float) ($itemData['selling_price'] ?? 0);
                $commType = $itemData['commission_type'] ?? 'percentage';
                $commVal = (float) ($itemData['commission_value'] ?? 0);

                $consignorAmount = $itemData['consignor_amount'] ?? $commissionService->calculateConsignorAmount($selling, $commType, $commVal);

                $consignment->items()->create([
                    'product_id' => $itemData['product_id'] ?? null,
                    'category_id' => $itemData['category_id'] ?? null,
                    'product_name' => $itemData['product_name'],
                    'quantity' => $qty,
                    'purchase_price' => $itemData['purchase_price'] ?? 0,
                    'selling_price' => $selling,
                    'commission_type' => $commType,
                    'commission_value' => $commVal,
                    'consignor_amount' => $consignorAmount,
                    'status' => 'pending',
                    'notes' => $itemData['notes'] ?? null,
                ]);

                $totalQty += $qty;
            }

            $consignment->update(['total_items' => $totalQty]);

            return $consignment->load('items');
        });
    }

    /**
     * Setujui (Approve) consignment:
     * - Mengubah status consignment menjadi approved
     * - Otomatis membuat/mengaktifkan record Product agar langsung siap dijual (available) di POS
     * - Mencatat snapshot harga & komisi
     */
    public function approveConsignment(Consignment $consignment): void
    {
        DB::transaction(function () use ($consignment) {
            $defaultCategory = Category::first();

            foreach ($consignment->items as $item) {
                if ($item->status !== 'rejected') {
                    // Jika belum ada product terkait, buat produk baru
                    if (!$item->product_id) {
                        $product = Product::create([
                            'category_id' => $item->category_id ?? $defaultCategory?->id,
                            'consignor_id' => $consignment->consignor_id,
                            'name' => $item->product_name,
                            'purchase_price' => $item->purchase_price,
                            'selling_price' => $item->selling_price,
                            'consignor_price' => $item->consignor_amount,
                            'commission_type' => $item->commission_type,
                            'commission_value' => $item->commission_value,
                            'stock' => $item->quantity,
                            'status' => 'available',
                            'received_at' => $consignment->received_date ?? now(),
                        ]);

                        $item->product_id = $product->id;
                    } else {
                        // Jika sudah ada produk, tambah stoknya dan set available
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                            $product->update(['status' => 'available']);
                        }
                    }

                    $item->status = 'approved';
                    $item->save();
                }
            }

            $consignment->status = 'approved';
            $consignment->save();
        });
    }

    /**
     * Tolak (Reject) consignment.
     */
    public function rejectConsignment(Consignment $consignment, ?string $reason = null): void
    {
        DB::transaction(function () use ($consignment, $reason) {
            $consignment->status = 'rejected';
            if ($reason) {
                $consignment->notes = ($consignment->notes ? $consignment->notes . "\n" : '') . "Alasan Ditolak: " . $reason;
            }
            $consignment->save();

            $consignment->items()->update(['status' => 'rejected']);
        });
    }
}
