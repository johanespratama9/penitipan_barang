<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsignorBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignor_id',
        'total_sales',
        'total_commission',
        'total_earned',
        'total_paid',
        'balance',
    ];

    protected $casts = [
        'total_sales' => 'decimal:2',
        'total_commission' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function consignor(): BelongsTo
    {
        return $this->belongsTo(Consignor::class);
    }
}

