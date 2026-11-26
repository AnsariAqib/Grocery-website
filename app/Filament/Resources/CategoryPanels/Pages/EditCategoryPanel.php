<?php

namespace App\Filament\Resources\CategoryPanels\Pages;

use App\Filament\Resources\CategoryPanels\CategoryPanelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategoryPanel extends EditRecord
{
    protected static string $resource = CategoryPanelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
