<?php

namespace Nurdaulet\FluxOrders\Filament\Resources\CanceledOrderResource\Pages;

use Nurdaulet\FluxOrders\Filament\Resources\CanceledOrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCanceledOrder extends CreateRecord
{
    protected static string $resource = CanceledOrderResource::class;
}
