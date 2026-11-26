<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    /**
     * Save and Cancel sit at the top of the screen.
     *
     * The product form is long — photos, pack sizes, badges — and Filament
     * puts its buttons under the last field by default, so saving a change to
     * the name meant scrolling to the bottom of a page that had nothing else
     * to do with it. Delete stays out of this row and goes to the foot of the
     * form instead, where it cannot be hit on the way to Save.
     */
    protected function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()->formId('form'),
            $this->getCancelFormAction(),
        ];
    }

    /**
     * The bottom of the form: Delete alone.
     */
    protected function getFormActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Delete this product')
                ->outlined(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }
}
