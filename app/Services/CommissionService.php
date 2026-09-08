<?php

namespace App\Services;

class CommissionService
{
    /**
     * Hitung pembagian komisi toko dan pendapatan penitip.
     *
     * @param float $sellingPrice Harga jual produk ke pembeli
     * @param string $commissionType Tipe komisi: 'percentage' atau 'fixed'
     * @param float $commissionValue Nilai komisi (% atau nominal Rp)
     * @return array{store_commission: float, consignor_amount: float}
     */
    public function calculate(float $sellingPrice, string $commissionType, float $commissionValue): array
    {
        $sellingPrice = max(0, $sellingPrice);
        $commissionValue = max(0, $commissionValue);

        if ($commissionType === 'percentage') {
            // Contoh: 20% komisi toko dari Rp 100.000 = Rp 20.000 toko, Rp 80.000 penitip
            $storeCommission = round(($sellingPrice * $commissionValue) / 100, 2);
        } else {
            // Fixed komisi: Rp 10.000 komisi toko
            $storeCommission = min($sellingPrice, round($commissionValue, 2));
        }

        $consignorAmount = max(0, $sellingPrice - $storeCommission);

        return [
            'store_commission' => $storeCommission,
            'consignor_amount' => $consignorAmount,
        ];
    }

    /**
     * Hitung pendapatan penitip (consignor_amount)
     */
    public function calculateConsignorAmount(float $sellingPrice, string $commissionType, float $commissionValue): float
    {
        return $this->calculate($sellingPrice, $commissionType, $commissionValue)['consignor_amount'];
    }

    /**
     * Hitung komisi toko (store_commission)
     */
    public function calculateStoreCommission(float $sellingPrice, string $commissionType, float $commissionValue): float
    {
        return $this->calculate($sellingPrice, $commissionType, $commissionValue)['store_commission'];
    }
}

