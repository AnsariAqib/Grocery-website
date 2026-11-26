<?php

namespace App\Filament\Resources\UspCards\Pages;

use App\Filament\Resources\UspCards\UspCardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUspCard extends EditRecord
{
    protected static string $resource = UspCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
