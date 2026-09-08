<?php

namespace App\Filament\Resources\ConsignorResource\Pages;

use App\Filament\Resources\ConsignorResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewConsignor extends ViewRecord
{
    protected static string $resource = ConsignorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

