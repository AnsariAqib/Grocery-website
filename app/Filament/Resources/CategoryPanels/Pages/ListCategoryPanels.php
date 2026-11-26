<?php

namespace App\Filament\Resources\CategoryPanels\Pages;

use App\Filament\Resources\CategoryPanels\CategoryPanelResource;
use Filament\Resources\Pages\ListRecords;

class ListCategoryPanels extends ListRecords
{
    protected static string $resource = CategoryPanelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
