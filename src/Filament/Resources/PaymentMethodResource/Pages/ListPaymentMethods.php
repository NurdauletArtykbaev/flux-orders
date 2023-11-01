<?php

namespace Nurdaulet\FluxOrders\Filament\Resources\PaymentMethodResource\Pages;

use Nurdaulet\FluxOrders\Filament\Resources\PaymentMethodResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPaymentMethods extends ListRecords
{
    use ListRecords\Concerns\Translatable;
    protected static string $resource = PaymentMethodResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
