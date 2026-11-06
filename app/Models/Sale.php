<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'cashier_id',
        'customer_name',
        'table_number',
        'order_notes',
        'kitchen_status',
        'subtotal',
        'discount',
        'tax',
        'total',
        'paid_amount',
        'change_amount',
        'payment_method',
        'status',
        'sold_at',
        'kitchen_printed_at',
        'cooking_started_at',
        'cooking_finished_at',
        'kitchen_completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'sold_at' => 'datetime',
        'kitchen_printed_at' => 'datetime',
        'cooking_started_at' => 'datetime',
        'cooking_finished_at' => 'datetime',
        'kitchen_completed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Sale $sale) {
            if (empty($sale->invoice_number)) {
                $sale->invoice_number = static::generateInvoiceNumber();
            }

            if (empty($sale->sold_at)) {
                $sale->sold_at = now();
            }

            if (empty($sale->kitchen_status)) {
                $sale->kitchen_status = 'pending';
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "INV-{$date}-";

        $lastSale = static::where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastSale) {
            $lastNum = (int) substr($lastSale->invoice_number, -4);
            $nextNumber = $lastNum + 1;
        }

        return sprintf('%s%04d', $prefix, $nextNumber);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope untuk pesanan yang masih aktif di dapur (belum full selesai)
     */
    public function scopeKitchenActive($query)
    {
        return $query->whereIn('kitchen_status', ['pending', 'cooking', 'ready']);
    }

    public function scopeKitchenPending($query)
    {
        return $query->where('kitchen_status', 'pending');
    }

    public function scopeKitchenCooking($query)
    {
        return $query->where('kitchen_status', 'cooking');
    }

    public function scopeKitchenReady($query)
    {
        return $query->where('kitchen_status', 'ready');
    }

    public function scopeKitchenCompleted($query)
    {
        return $query->where('kitchen_status', 'completed');
    }

    public function getKitchenStatusLabelAttribute(): string
    {
        return match ($this->kitchen_status) {
            'pending'   => 'Menunggu Dapur',
            'cooking'   => 'Sedang Dimasak',
            'ready'     => 'Siap Saji',
            'completed' => 'Full Selesai (All Done)',
            'cancelled' => 'Dibatalkan',
            default     => ucfirst($this->kitchen_status ?? 'Menunggu'),
        };
    }

    public function getKitchenStatusColorAttribute(): string
    {
        return match ($this->kitchen_status) {
            'pending'   => 'warning',
            'cooking'   => 'info',
            'ready'     => 'success',
            'completed' => 'gray',
            'cancelled' => 'danger',
            default     => 'gray',
        };
    }
}
