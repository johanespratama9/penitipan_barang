<?php

namespace App\Filament\Resources\ConsignorPaymentResource\Pages;

use App\Filament\Resources\ConsignorPaymentResource;
use App\Models\Consignor;
use App\Services\ConsignorBalanceService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateConsignorPayment extends CreateRecord
{
    protected static string $resource = ConsignorPaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $consignor = Consignor::with('balance')->findOrFail($data['consignor_id']);
        $balance = (float) ($consignor->balance?->balance ?? 0);
        $amount = (float) $data['amount'];

        if ($amount > $balance) {
            Notification::make()
                ->title('Gagal Memproses Pembayaran')
                ->body('Nominal pembayaran melebihi saldo tersedia (Rp ' . number_format($balance, 0, ',', '.') . ')')
                ->danger()
                ->send();

            $this->halt();
        }

        $data['paid_by'] = auth()->id() ?? 1;

        return $data;
    }

    protected function afterCreate(): void
    {
        $payment = $this->getRecord();
        app(ConsignorBalanceService::class)->recordPayment($payment->consignor, (float) $payment->amount);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

