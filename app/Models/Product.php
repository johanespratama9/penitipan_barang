<?php

namespace App\Models;

use App\Services\CommissionService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'consignor_id',
        'code',
        'barcode',
        'name',
        'description',
        'purchase_price',
        'selling_price',
        'consignor_price',
        'commission_type',
        'commission_value',
        'stock',
        'status',
        'condition',
        'image',
        'received_at',
        'sold_at',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'consignor_price' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'stock' => 'integer',
        'received_at' => 'datetime',
        'sold_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->code)) {
                $product->code = static::generateUniqueCode();
            }

            if (empty($product->received_at) && in_array($product->status, ['available', 'pending'])) {
                $product->received_at = now();
            }

            // Hitung otomatis consignor_price jika belum diset secara eksplisit
            if ($product->consignor_price <= 0 && $product->selling_price > 0) {
                $service = app(CommissionService::class);
                $product->consignor_price = $service->calculateConsignorAmount(
                    (float) $product->selling_price,
                    $product->commission_type ?? 'percentage',
                    (float) $product->commission_value
                );
            }
        });

        static::updating(function (Product $product) {
            // Hitung ulang jika harga jual atau komisi berubah
            if (($product->isDirty('selling_price') || $product->isDirty('commission_value') || $product->isDirty('commission_type')) && !$product->isDirty('consignor_price')) {
                $service = app(CommissionService::class);
                $product->consignor_price = $service->calculateConsignorAmount(
                    (float) $product->selling_price,
                    $product->commission_type ?? 'percentage',
                    (float) $product->commission_value
                );
            }

            // Jika status berubah ke sold dan sold_at kosong
            if ($product->isDirty('status') && $product->status === 'sold' && empty($product->sold_at)) {
                $product->sold_at = now();
            }
        });
    }

    /**
     * Generate unique product code: PRD-00001, PRD-00002, etc.
     */
    public static function generateUniqueCode(): string
    {
        $lastId = static::max('id') ?? 0;
        $nextNumber = $lastId + 1;
        $code = sprintf('PRD-%05d', $nextNumber);

        while (static::where('code', $code)->exists()) {
            $nextNumber++;
            $code = sprintf('PRD-%05d', $nextNumber);
        }

        return $code;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function consignor(): BelongsTo
    {
        return $this->belongsTo(Consignor::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('stock', '>', 0);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }
}

