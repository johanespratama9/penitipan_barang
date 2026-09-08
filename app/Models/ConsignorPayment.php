<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsignorPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignor_id',
        'payment_number',
        'amount',
        'payment_method',
        'reference_number',
        'paid_at',
        'paid_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ConsignorPayment $payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = static::generatePaymentNumber();
            }

            if (empty($payment->paid_at)) {
                $payment->paid_at = now();
            }
        });
    }

    public static function generatePaymentNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "PAY-{$date}-";

        $last = static::where('payment_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($last) {
            $lastNum = (int) substr($last->payment_number, -4);
            $nextNumber = $lastNum + 1;
        }

        return sprintf('%s%04d', $prefix, $nextNumber);
    }

    public function consignor(): BelongsTo
    {
        return $this->belongsTo(Consignor::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}

