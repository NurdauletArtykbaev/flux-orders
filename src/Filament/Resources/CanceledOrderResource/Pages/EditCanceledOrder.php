<?php

namespace Nurdaulet\FluxOrders\Filament\Resources\CanceledOrderResource\Pages;

use Nurdaulet\FluxOrders\Filament\Resources\CanceledOrderResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCanceledOrder extends EditRecord
{
    protected static string $resource = CanceledOrderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
