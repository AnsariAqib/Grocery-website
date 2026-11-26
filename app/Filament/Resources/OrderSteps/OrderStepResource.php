<?php

namespace App\Filament\Resources\OrderSteps;

use App\Filament\Resources\OrderSteps\Pages\CreateOrderStep;
use App\Filament\Resources\OrderSteps\Pages\EditOrderStep;
use App\Filament\Resources\OrderSteps\Pages\ListOrderSteps;
use App\Filament\Resources\OrderSteps\Schemas\OrderStepForm;
use App\Filament\Resources\OrderSteps\Tables\OrderStepsTable;
use App\Models\OrderStep;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OrderStepResource extends Resource
{
    protected static ?string $model = OrderStep::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static string|\UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?string $navigationLabel = 'B2B Order Process';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Order step';

    protected static ?string $pluralModelLabel = 'B2B Order Process';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return OrderStepForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrderStepsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrderSteps::route('/'),
            'create' => CreateOrderStep::route('/create'),
            'edit' => EditOrderStep::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}
