<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages\EditSetting;
use App\Filament\Resources\Settings\Pages\ListSettings;
use App\Models\Setting;
use App\Support\FontOptions;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema(fn (?Setting $record): array => [
                    TextInput::make('label')
                        ->label('Setting')
                        ->disabled()
                        ->dehydrated(false),

                    self::valueField($record),
                ])
                ->description(fn (?Setting $record) => $record?->help)
                ->columns(1),
        ]);
    }

    /**
     * Exactly one field for `value`, chosen up front from the setting's type.
     *
     * Building all three and hiding two does not work: they would share the
     * `value` state path, and the toggle hydrates even while hidden — casting
     * a stored address to a boolean and wiping it out of the form.
     */
    private static function valueField(?Setting $record): TextInput|Textarea|Toggle|Select
    {
        $type = $record?->type;

        if ($type === 'select') {
            return Select::make('value')
                ->label('Value')
                ->options(self::selectOptions($record?->key))
                ->native(false)
                ->searchable()
                ->required();
        }

        if ($type === 'boolean') {
            return Toggle::make('value')
                ->label('Enabled')
                ->afterStateHydrated(fn (Toggle $component, $state) => $component->state(filter_var($state, FILTER_VALIDATE_BOOLEAN)))
                ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0');
        }

        if ($type === 'textarea') {
            return Textarea::make('value')
                ->label('Value')
                ->rows(3)
                ->maxLength(1000);
        }

        return TextInput::make('value')
            ->label('Value')
            ->maxLength(255)
            ->email($type === 'email')
            ->url($type === 'url')
            ->rules($type === 'phone' ? ['regex:/^[0-9+\s\-()]+$/'] : [])
            ->validationMessages([
                'regex' => 'Use digits only, optionally with +, spaces, dashes or brackets.',
            ]);
    }

    /**
     * Choices for a `select` setting, keyed by the setting itself.
     *
     * Every select needs its own list, so new ones get a case here rather than
     * a column of serialised options on the table.
     */
    private static function selectOptions(?string $key): array
    {
        return match ($key) {
            'font_heading', 'font_body' => FontOptions::options(),
            default => [],
        };
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->searchable()
                    ->weight('semibold'),

                TextColumn::make('value')
                    ->limit(60)
                    ->formatStateUsing(fn ($state, Setting $record) => match ($record->type) {
                        'boolean' => filter_var($state, FILTER_VALIDATE_BOOLEAN) ? 'On' : 'Off',
                        // Stored value is a slug; the list should read like the dropdown.
                        'select' => self::selectOptions($record->key)[$state] ?? $state,
                        default => $state,
                    })
                    ->badge(fn (Setting $record) => $record->type === 'boolean')
                    ->color(fn ($state, Setting $record) => $record->type !== 'boolean'
                        ? null
                        : (filter_var($state, FILTER_VALIDATE_BOOLEAN) ? 'success' : 'gray')),

                TextColumn::make('group')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultGroup('group')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            'edit' => EditSetting::route('/{record}/edit'),
        ];
    }

    // Settings are seeded as a fixed set of keys; the site reads them by name.
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
