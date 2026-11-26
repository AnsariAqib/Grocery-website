<?php

namespace App\Filament\Resources\PromoPopups\Pages;

use App\Filament\Resources\PromoPopups\PromoPopupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPromoPopups extends ListRecords
{
    protected static string $resource = PromoPopupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
