<?php

namespace App\Filament\Resources\Subcategories\Pages;

use App\Filament\Resources\Subcategories\SubcategoryResource;
use App\Models\Category;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSubcategories extends ListRecords
{
    protected static string $resource = SubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Subcategories'),
            'store' => Tab::make('Store Categories')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('category', fn ($q) => $q->where('type', Category::TYPE_STORE))),
            'b2b' => Tab::make('B2B Categories')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('category', fn ($q) => $q->where('type', Category::TYPE_B2B))),
        ];
    }
}
