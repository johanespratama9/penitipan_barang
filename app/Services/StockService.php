<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Catat pergerakan stok (in, out, adjustment, return) dan perbarui stok produk.
     */
    public function recordStockMovement(
        Product $product,
        int $quantity,
        string $type,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $userId = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $quantity, $type, $referenceType, $referenceId, $notes, $userId) {
            $currentStock = $product->stock;

            if ($type === 'in' || $type === 'return') {
                $newStock = $currentStock + abs($quantity);
            } elseif ($type === 'out') {
                if ($currentStock < abs($quantity)) {
                    throw new \Exception("Stok tidak mencukupi untuk produk {$product->name}. Stok saat ini: {$currentStock}, diminta: {$quantity}");
                }
                $newStock = $currentStock - abs($quantity);
            } elseif ($type === 'adjustment') {
                // Untuk adjustment, quantity bisa positif (penambahan) atau negatif (pengurangan)
                $newStock = max(0, $currentStock + $quantity);
            } else {
                throw new \InvalidArgumentException("Tipe stock movement tidak valid: {$type}");
            }

            // Update stok produk
            $product->stock = $newStock;

            // Jika stok habis (0), status menjadi sold jika sebelumnya available
            if ($newStock === 0 && $product->status === 'available') {
                $product->status = 'sold';
                $product->sold_at = now();
            } elseif ($newStock > 0 && $product->status === 'sold') {
                $product->status = 'available';
            }

            $product->save();

            // Simpan riwayat mutasi stok
            return StockMovement::create([
                'product_id' => $product->id,
                'type' => $type,
                'quantity' => $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'created_by' => $userId ?? auth()->id(),
            ]);
        });
    }
}

