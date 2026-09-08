<?php

namespace App\Filament\Resources\ConsignmentResource\Pages;

use App\Filament\Resources\ConsignmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateConsignment extends CreateRecord
{
    protected static string $resource = ConsignmentResource::class;

    protected function afterCreate(): void
    {
        $this->getRecord()->updateTotalItems();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
