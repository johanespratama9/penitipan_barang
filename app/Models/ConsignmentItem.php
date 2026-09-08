<?php

namespace App\Models;

use App\Services\CommissionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsignmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignment_id',
        'product_id',
        'category_id',
        'product_name',
        'quantity',
        'purchase_price',
        'selling_price',
        'commission_type',
        'commission_value',
        'consignor_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'consignor_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (ConsignmentItem $item) {
            // Hitung otomatis consignor_amount dari harga jual & komisi jika belum diisi
            if ($item->consignor_amount <= 0 && $item->selling_price > 0) {
                $service = app(CommissionService::class);
                $item->consignor_amount = $service->calculateConsignorAmount(
                    (float) $item->selling_price,
                    $item->commission_type ?? 'percentage',
                    (float) $item->commission_value
                );
            }
        });
    }

    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
