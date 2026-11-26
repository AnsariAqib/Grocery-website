<?php

namespace App\Filament\Resources\OrderSteps\Pages;

use App\Filament\Resources\OrderSteps\OrderStepResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrderStep extends CreateRecord
{
    protected static string $resource = OrderStepResource::class;
}
