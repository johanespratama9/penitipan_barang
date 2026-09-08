<?php

namespace App\Filament\Resources\ConsignorResource\Pages;

use App\Filament\Resources\ConsignorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateConsignor extends CreateRecord
{
    protected static string $resource = ConsignorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

