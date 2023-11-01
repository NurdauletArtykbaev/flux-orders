<?php

namespace Nurdaulet\FluxOrders\Filament\Resources\CanceledOrderResource\Pages;

use Nurdaulet\FluxOrders\Filament\Resources\CanceledOrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCanceledOrders extends ListRecords
{
    protected static string $resource = CanceledOrderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
