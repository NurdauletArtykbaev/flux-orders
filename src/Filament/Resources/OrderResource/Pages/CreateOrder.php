<?php

namespace Nurdaulet\FluxOrders\Filament\Resources\OrderResource\Pages;

use Nurdaulet\FluxOrders\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
}
