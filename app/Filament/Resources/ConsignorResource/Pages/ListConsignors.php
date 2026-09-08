<?php

namespace App\Filament\Resources\ConsignorResource\Pages;

use App\Filament\Resources\ConsignorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConsignors extends ListRecords
{
    protected static string $resource = ConsignorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

