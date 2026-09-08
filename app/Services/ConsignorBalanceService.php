<?php

namespace App\Services;

use App\Models\Consignor;
use App\Models\ConsignorBalance;
use App\Models\ConsignorPayment;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class ConsignorBalanceService
{
    /**
     * Dapatkan atau buat record saldo untuk penitip
     */
    public function getOrCreateBalance(Consignor $consignor): ConsignorBalance
    {
        return ConsignorBalance::firstOrCreate(
            ['consignor_id' => $consignor->id],
            [
                'total_sales' => 0,
                'total_commission' => 0,
                'total_earned' => 0,
                'total_paid' => 0,
                'balance' => 0,
            ]
        );
    }

    /**
     * Catat penambahan saldo dari penjualan produk
     */
    public function recordSale(Consignor $consignor, float $salesAmount, float $commissionAmount, float $earnedAmount): ConsignorBalance
    {
        return DB::transaction(function () use ($consignor, $salesAmount, $commissionAmount, $earnedAmount) {
            $balance = $this->getOrCreateBalance($consignor);

            $balance->total_sales += $salesAmount;
            $balance->total_commission += $commissionAmount;
            $balance->total_earned += $earnedAmount;
            $balance->balance += $earnedAmount;
            $balance->save();

            return $balance;
        });
    }

    /**
     * Catat pengurangan saldo dari pembayaran/pencairan dana
     */
    public function recordPayment(Consignor $consignor, float $paymentAmount): ConsignorBalance
    {
        return DB::transaction(function () use ($consignor, $paymentAmount) {
            $balance = $this->getOrCreateBalance($consignor);

            $balance->total_paid += $paymentAmount;
            $balance->balance = max(0, $balance->balance - $paymentAmount);
            $balance->save();

            return $balance;
        });
    }

    /**
     * Hitung ulang saldo penitip dari data transaksi asli (ground truth)
     */
    public function recalculate(Consignor $consignor): ConsignorBalance
    {
        return DB::transaction(function () use ($consignor) {
            $balance = $this->getOrCreateBalance($consignor);

            // Hitung total dari seluruh sale_items produk milik penitip ini
            $salesData = SaleItem::whereHas('product', fn ($q) => $q->where('consignor_id', $consignor->id))
                ->selectRaw('
                    COALESCE(SUM(subtotal), 0) as total_sales,
                    COALESCE(SUM(commission_amount), 0) as total_commission,
                    COALESCE(SUM(consignor_amount), 0) as total_earned
                ')
                ->first();

            $totalPaid = (float) ConsignorPayment::where('consignor_id', $consignor->id)->sum('amount');

            $totalSales = (float) ($salesData->total_sales ?? 0);
            $totalCommission = (float) ($salesData->total_commission ?? 0);
            $totalEarned = (float) ($salesData->total_earned ?? 0);
            $currentBalance = max(0, $totalEarned - $totalPaid);

            $balance->update([
                'total_sales' => $totalSales,
                'total_commission' => $totalCommission,
                'total_earned' => $totalEarned,
                'total_paid' => $totalPaid,
                'balance' => $currentBalance,
            ]);

            return $balance;
        });
    }
}

