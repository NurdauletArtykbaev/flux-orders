<?php

namespace Nurdaulet\FluxOrders\Filament\Resources\CancelReasonResource\Pages;

use Nurdaulet\FluxOrders\Filament\Resources\CancelReasonResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCancelReason extends EditRecord
{
    protected static string $resource = CancelReasonResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
