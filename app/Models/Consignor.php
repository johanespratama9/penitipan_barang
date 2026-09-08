<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consignor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'name',
        'phone',
        'email',
        'address',
        'identity_number',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected static function booted(): void
    {
        static::creating(function (Consignor $consignor) {
            if (empty($consignor->code)) {
                $consignor->code = static::generateUniqueCode();
            }
        });
    }

    /**
     * Generate unique consignor code: PEN-00001, PEN-00002, etc.
     */
    public static function generateUniqueCode(): string
    {
        $lastId = static::max('id') ?? 0;
        $nextNumber = $lastId + 1;
        $code = sprintf('PEN-%05d', $nextNumber);

        while (static::where('code', $code)->exists()) {
            $nextNumber++;
            $code = sprintf('PEN-%05d', $nextNumber);
        }

        return $code;
    }

    /**
     * Relasi ke akun User (jika penitip memiliki akses login)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk penitip aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Daftar barang yang dititipkan oleh penitip ini
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Daftar dokumen penitipan barang
     */
    public function consignments()
    {
        return $this->hasMany(Consignment::class);
    }

    /**
     * Saldo dan ringkasan pendapatan penitip
     */
    public function balance()
    {
        return $this->hasOne(ConsignorBalance::class);
    }

    /**
     * Riwayat pembayaran yang sudah diterima penitip
     */
    public function payments()
    {
        return $this->hasMany(ConsignorPayment::class);
    }
}

