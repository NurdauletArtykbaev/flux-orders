<?php

namespace Nurdaulet\FluxOrders;


use Filament\PluginServiceProvider;
use Nurdaulet\FluxOrders\Filament\Resources\OrderResource;
use Nurdaulet\FluxOrders\Filament\Resources\CanceledOrderResource;
use Nurdaulet\FluxOrders\Filament\Resources\PaymentMethodResource;
use Nurdaulet\FluxOrders\Filament\Resources\CancelReasonResource;
use Spatie\LaravelPackageTools\Package;

class FluxOrdersFilamentServiceProvider extends PluginServiceProvider
{
    protected array $resources = [
        OrderResource::class,
        CanceledOrderResource::class,
        CancelReasonResource::class,
        PaymentMethodResource::class
    ];

    public function configurePackage(Package $package): void
    {
        $this->packageConfiguring($package);
        $package->name('orders-package');
    }
}
