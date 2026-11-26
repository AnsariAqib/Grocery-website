<?php

namespace App\Filament\Resources\GalleryImages\Schemas;

use App\Models\GalleryImage;
use App\Models\Reel;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class GalleryImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Gallery item')
                ->description('Photos and videos shown in the gallery, and in the carousel beside the Featured sections on the home page. Photos and videos appear under their own headings on the site.')
                ->schema([
                    Radio::make('media_type')
                        ->label('This item is')
                        ->options([
                            GalleryImage::TYPE_PHOTO => 'A photo',
                            GalleryImage::TYPE_VIDEO => 'A YouTube video',
                        ])
                        ->default(GalleryImage::TYPE_PHOTO)
                        ->inline()
                        ->inlineLabel(false)
                        ->live()
                        ->columnSpanFull(),

                    TextInput::make('youtube_url')
                        ->label('YouTube link')
                        ->url()
                        ->maxLength(255)
                        ->placeholder('https://www.youtube.com/watch?v=d7rT-4xfqGU')
                        ->helperText('Paste the link — the video is not uploaded, it plays from YouTube. Any share format works: youtu.be, /watch?v=, /shorts/ or /embed/.')
                        ->required(fn (Get $get) => $get('media_type') === GalleryImage::TYPE_VIDEO)
                        ->visible(fn (Get $get) => $get('media_type') === GalleryImage::TYPE_VIDEO)
                        // Validated with the same rule the site renders by, so a
                        // link that would show a blank frame is refused here.
                        ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                            if (filled($value) && Reel::youtubeIdFrom($value) === null) {
                                $fail('That does not look like a YouTube link. Copy it from the video\'s Share button.');
                            }
                        })
                        ->columnSpanFull(),

                    FileUpload::make('image')
                        ->label(fn (Get $get) => $get('media_type') === GalleryImage::TYPE_VIDEO ? 'Cover image (optional)' : 'Photo')
                        ->image()
                        ->disk('public')
                        ->directory('gallery')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->required(fn (Get $get) => $get('media_type') !== GalleryImage::TYPE_VIDEO)
                        ->helperText(fn (Get $get) => $get('media_type') === GalleryImage::TYPE_VIDEO
                            ? 'Leave empty and YouTube\'s own still is used. Upload one only if you want a particular frame.'
                            : 'Portrait or square crops sit best in the rail.')
                        ->columnSpanFull(),

                    TextInput::make('title')
                        ->maxLength(255)
                        ->placeholder('Inside Vinayak Stores'),

                    TextInput::make('caption')
                        ->maxLength(255)
                        ->placeholder('AMNS Township, Hazira'),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers show first.'),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->columnSpanFull(),

                    DateTimePicker::make('publish_at')
                        ->label('Publish Date & Time')
                        ->helperText('Leave blank to publish immediately.'),

                    DateTimePicker::make('expire_at')
                        ->label('Expiry Date & Time')
                        ->helperText('Leave blank to never expire.'),
                ])
                ->columns(2),
        ]);
    }
}
