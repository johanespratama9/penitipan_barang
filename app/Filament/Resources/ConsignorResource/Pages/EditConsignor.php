<?php

namespace App\Filament\Resources\ConsignorResource\Pages;

use App\Filament\Resources\ConsignorResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditConsignor extends EditRecord
{
    protected static string $resource = ConsignorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

