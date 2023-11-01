<?php

namespace Nurdaulet\FluxOrders\Filament\Resources\CancelReasonResource\Pages;

use Nurdaulet\FluxOrders\Filament\Resources\CancelReasonResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCancelReasons extends ListRecords
{
    protected static string $resource = CancelReasonResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
