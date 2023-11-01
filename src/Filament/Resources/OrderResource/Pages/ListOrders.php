<?php

namespace Nurdaulet\FluxOrders\Filament\Resources\OrderResource\Pages;

use Nurdaulet\FluxOrders\Filament\Resources\OrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
