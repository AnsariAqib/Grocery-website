<?php

namespace App\Filament\Resources\OrderSteps\Pages;

use App\Filament\Resources\OrderSteps\OrderStepResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrderStep extends EditRecord
{
    protected static string $resource = OrderStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
