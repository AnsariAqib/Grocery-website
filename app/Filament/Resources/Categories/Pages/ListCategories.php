<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Categories'),
            'store' => Tab::make('Store Categories')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', Category::TYPE_STORE)),
            'b2b' => Tab::make('B2B Categories')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', Category::TYPE_B2B)),
        ];
    }
}
