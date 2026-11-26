<?php

namespace App\Filament\Resources\Certificates\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // ── Step 1: Choose certificate type ──
            Section::make('Certificate Type')
                ->description('Choose how you want to add this certificate.')
                ->schema([
                    Radio::make('type')
                        ->label('Certificate Mode')
                        ->options([
                            'original' => '📄 Upload Original Certificate — Upload your real scanned certificate (Image/PDF), it will display as-is on the website.',
                            'custom' => '🎨 Custom Certificate — Enter brand logo & details, we\'ll generate a styled certificate card automatically.',
                        ])
                        ->default('custom')
                        ->required()
                        ->live()
                        ->columnSpanFull(),

                    TextInput::make('brand_name')
                        ->label('Brand Name')
                        ->placeholder('e.g. Colgate, Dabur, Godrej, Parle')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers appear first on the home page.'),

                    Toggle::make('is_active')
                        ->label('Active on Website')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // ── Section for ORIGINAL certificate upload ──
            Section::make('Upload Original Certificate')
                ->description('Upload the scanned certificate image (JPG/PNG) or PDF. It will display exactly as uploaded on the home page.')
                ->schema([
                    FileUpload::make('certificate_image')
                        ->label('Certificate Image (JPG / PNG)')
                        ->image()
                        ->disk('public')
                        ->directory('certificates/scans')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->openable()
                        ->downloadable()
                        ->helperText('Upload the full scanned certificate image. This will display as-is in the popup modal on the website.'),

                    FileUpload::make('pdf_file')
                        ->label('Certificate PDF Document')
                        ->disk('public')
                        ->directory('certificates/pdf')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(10240)
                        ->openable()
                        ->downloadable()
                        ->helperText('Optional. Upload the official PDF certificate (Max 10MB). Enables "View PDF" button in the popup.'),

                    FileUpload::make('logo_image')
                        ->label('Brand Logo (for marquee card)')
                        ->image()
                        ->disk('public')
                        ->directory('certificates/logos')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->helperText('Optional. If provided, this logo is shown on the marquee strip card. Otherwise the certificate image thumbnail is used.'),
                ])
                ->columns(2)
                ->visible(fn ($get) => $get('type') === 'original'),

            // ── Section for CUSTOM styled certificate ──
            Section::make('Custom Certificate Details')
                ->description('Enter the brand logo and certificate details. The website will generate a professionally styled certificate card from these details. No PDF or full image needed.')
                ->schema([
                    FileUpload::make('logo_image')
                        ->label('Brand Logo')
                        ->image()
                        ->disk('public')
                        ->directory('certificates/logos')
                        ->maxSize(25600)
                        ->imageEditor()
                        ->required()
                        ->helperText('Brand logo displayed on both the marquee card and the certificate popup.'),

                    TextInput::make('title')
                        ->label('Certificate Title')
                        ->maxLength(255)
                        ->placeholder('OFFICIAL AGENCY / AUTHORISED DISTRIBUTOR')
                        ->helperText('The title text displayed on the certificate card.'),

                    Textarea::make('description')
                        ->label('Description / Scope')
                        ->placeholder('e.g. Authorised distributor for Colgate oral hygiene care.')
                        ->rows(3)
                        ->columnSpanFull(),

                    TextInput::make('signatory')
                        ->label('Signatory / Company Name')
                        ->maxLength(255)
                        ->placeholder('e.g. Colgate-Palmolive (India) Ltd.'),
                ])
                ->columns(2)
                ->visible(fn ($get) => $get('type') === 'custom'),
        ]);
    }
}
