<?php

namespace App\Filament\Resources\Subcategories\Pages;

use App\Filament\Resources\Subcategories\SubcategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubcategory extends CreateRecord
{
    protected static string $resource = SubcategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
