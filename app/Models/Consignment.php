<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'consignor_id',
        'code',
        'received_date',
        'expiry_date',
        'status',
        'notes',
        'total_items',
    ];

    protected $casts = [
        'received_date' => 'date',
        'expiry_date' => 'date',
        'total_items' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Consignment $consignment) {
            if (empty($consignment->code)) {
                $consignment->code = static::generateUniqueCode();
            }

            if (empty($consignment->received_date)) {
                $consignment->received_date = now()->toDateString();
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        $lastId = static::max('id') ?? 0;
        $nextNumber = $lastId + 1;
        $code = sprintf('CSG-%05d', $nextNumber);

        while (static::where('code', $code)->exists()) {
            $nextNumber++;
            $code = sprintf('CSG-%05d', $nextNumber);
        }

        return $code;
    }

    public function consignor(): BelongsTo
    {
        return $this->belongsTo(Consignor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ConsignmentItem::class);
    }

    public function updateTotalItems(): void
    {
        $this->update([
            'total_items' => $this->items()->sum('quantity') ?: $this->items()->count(),
        ]);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }
}
