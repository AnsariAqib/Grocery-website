<?php

namespace App\Filament\Resources\UspCards\Pages;

use App\Filament\Resources\UspCards\UspCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUspCards extends ListRecords
{
    protected static string $resource = UspCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
