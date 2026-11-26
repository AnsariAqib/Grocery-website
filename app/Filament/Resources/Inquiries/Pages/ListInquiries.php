<?php

namespace App\Filament\Resources\Inquiries\Pages;

use App\Filament\Resources\Inquiries\InquiryResource;
use App\Services\InquiryCsv;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListInquiries extends ListRecords
{
    protected static string $resource = InquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Everyday half: hand the leads to whoever works them in Excel.
            Action::make('export')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn (): StreamedResponse => app(InquiryCsv::class)->download()),

            // Other direction: leads gathered off the website (an old sheet, a
            // WhatsApp export, a trade-show list) land in the same inbox.
            Action::make('import')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->modalHeading('Import inquiries from a CSV')
                ->modalDescription('Needs at least a "Name" and a "Phone" column. A lead already listed with the same name and phone is skipped rather than duplicated.')
                ->modalSubmitActionLabel('Import')
                ->schema([
                    FileUpload::make('sheet')
                        ->label('CSV file')
                        ->required()
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                        ->storeFiles(false)
                        ->helperText('Columns: Name, Phone, Email, Company, Industry, Inquiry Type, Product, Category, Message, Submitted At. Extra columns are ignored.'),
                ])
                ->extraModalFooterActions([
                    Action::make('template')
                        ->label('Download a blank template')
                        ->link()
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(fn (): StreamedResponse => app(InquiryCsv::class)->template()),
                ])
                ->action(function (array $data): void {
                    $file = $data['sheet'] ?? null;

                    if (! $file instanceof TemporaryUploadedFile) {
                        Notification::make()->danger()->title('No file received.')->send();

                        return;
                    }

                    $result = app(InquiryCsv::class)->import($file->getRealPath());

                    if ($result['errors'] !== [] && $result['imported'] === 0) {
                        Notification::make()
                            ->danger()
                            ->title('Nothing was imported')
                            ->body(implode("\n", array_slice($result['errors'], 0, 5)))
                            ->persistent()
                            ->send();

                        return;
                    }

                    $parts = [$result['imported'].' imported'];
                    if ($result['duplicates'] > 0) {
                        $parts[] = $result['duplicates'].' already on the list';
                    }
                    if ($result['skipped'] > 0) {
                        $parts[] = $result['skipped'].' skipped';
                    }

                    Notification::make()
                        ->success()
                        ->title('Import finished')
                        ->body(implode(' · ', $parts))
                        ->send();
                }),
        ];
    }
}
