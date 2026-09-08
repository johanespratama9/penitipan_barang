<?php

namespace App\Services;

use App\Models\Consignor;
use App\Models\ConsignorPayment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected ConsignorBalanceService $balanceService;

    public function __construct(ConsignorBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    /**
     * Buat pembayaran/pencairan dana ke penitip dengan validasi saldo.
     */
    public function createPayment(
        Consignor $consignor,
        float $amount,
        string $method = 'transfer',
        ?string $reference = null,
        ?string $notes = null,
        ?int $adminId = null
    ): ConsignorPayment {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Nominal pembayaran harus lebih besar dari 0.');
        }

        return DB::transaction(function () use ($consignor, $amount, $method, $reference, $notes, $adminId) {
            $balance = $this->balanceService->getOrCreateBalance($consignor);

            if ($amount > (float) $balance->balance) {
                $formattedBalance = number_format((float) $balance->balance, 0, ',', '.');
                $formattedAmount = number_format($amount, 0, ',', '.');
                throw new \Exception("Nominal pembayaran (Rp {$formattedAmount}) melebihi saldo yang tersedia (Rp {$formattedBalance}).");
            }

            $payment = ConsignorPayment::create([
                'consignor_id' => $consignor->id,
                'payment_number' => ConsignorPayment::generatePaymentNumber(),
                'amount' => $amount,
                'payment_method' => $method,
                'reference_number' => $reference,
                'paid_at' => now(),
                'paid_by' => $adminId ?? auth()->id() ?? 1,
                'notes' => $notes,
            ]);

            // Update saldo penitip
            $this->balanceService->recordPayment($consignor, $amount);

            return $payment;
        });
    }
}

