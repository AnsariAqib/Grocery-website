<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Filament\Helpers\IconHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Review')
                ->description('The "What Our Clients & Partners Say" cards on the home page. Turn every review off and the whole section disappears.')
                ->schema([
                    Textarea::make('quote')
                        ->label('What they said')
                        ->rows(4)
                        ->required()
                        ->maxLength(600)
                        ->placeholder('Their bulk pricing on housekeeping and canteen supplies is unmatched, and delivery is always on time.')
                        ->helperText('Quotation marks are added by the card — type the words only.')
                        ->columnSpanFull(),

                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Rajesh Patel'),

                    TextInput::make('role')
                        ->label('Who they are')
                        ->maxLength(255)
                        ->placeholder('Procurement Lead, Facility Mgmt'),

                    TextInput::make('tag')
                        ->label('Pill')
                        ->maxLength(40)
                        ->placeholder('Corporate')
                        ->helperText('The small ticked label on the card — "Corporate", "Retail Customer". Leave blank to hide it.'),

                    Select::make('rating')
                        ->label('Stars')
                        ->options([5 => '5 stars', 4 => '4 stars', 3 => '3 stars', 2 => '2 stars', 1 => '1 star'])
                        ->default(5)
                        ->required(),

                    Select::make('icon')
                        ->label('Avatar icon')
                        ->options(IconHelper::getFontAwesomeSolidIcons())
                        ->allowHtml()
                        ->searchable()
                        ->default('fa-user-tie')
                        ->helperText('Stands in for a photo in the round avatar.'),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers come first.'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
