<?php

namespace App\Filament\Resources\Catalogues\Pages;

use App\Filament\Resources\Catalogues\CatalogueResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCatalogue extends EditRecord
{
    protected static string $resource = CatalogueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
