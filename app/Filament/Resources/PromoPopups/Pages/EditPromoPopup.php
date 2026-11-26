<?php

namespace App\Filament\Resources\PromoPopups\Pages;

use App\Filament\Resources\PromoPopups\PromoPopupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPromoPopup extends EditRecord
{
    protected static string $resource = PromoPopupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
