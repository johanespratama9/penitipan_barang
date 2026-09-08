<?php

namespace App\Filament\Resources\ConsignorPaymentResource\Pages;

use App\Filament\Resources\ConsignorPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConsignorPayments extends ListRecords
{
    protected static string $resource = ConsignorPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

