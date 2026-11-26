<?php

namespace App\Filament\Resources\Reels\Schemas;

use App\Models\Reel;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Short')
                ->schema([
                    TextInput::make('youtube_url')
                        ->label('YouTube Shorts link')
                        ->required()
                        ->url()
                        // Validated through the model rather than a regex rule so
                        // the admin can never save a link the storefront would
                        // then fail to play.
                        ->rule(static function (): Closure {
                            return static function (string $attribute, mixed $value, Closure $fail): void {
                                if (filled($value) && Reel::youtubeIdFrom($value) === null) {
                                    $fail("That doesn't look like a YouTube link. Open the Short, tap Share → Copy link, and paste it here (it looks like https://www.youtube.com/shorts/XXXXXXXXXXX).");
                                }
                            };
                        })
                        ->helperText('Paste the link and you are done — the video plays right on the website, and YouTube supplies the thumbnail. Works with youtube.com/shorts/…, youtu.be/… and watch?v=… links.')
                        ->columnSpanFull(),

                    TextInput::make('title')
                        ->label('Heading')
                        ->maxLength(255)
                        ->helperText('Shown under the video. Leave blank to show nothing.'),

                    Textarea::make('caption')
                        ->rows(2)
                        ->maxLength(500),

                    FileUpload::make('cover_image')
                        ->label('Custom cover image (optional)')
                        ->image()
                        ->disk('public')
                        ->directory('reels')
                        ->maxSize(24000)
                        ->imageEditor()
                        ->imageEditorAspectRatios(['9:16'])
                        ->helperText("Only needed if you don't like YouTube's own thumbnail. Portrait, ideally 1080×1920.")
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make()
                ->schema([
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Order on the promotions page, lowest first.'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('The whole shorts section disappears when no short is active.'),
                ])
                ->columns(2),
        ]);
    }
}
