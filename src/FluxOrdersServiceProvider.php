<?php

namespace Nurdaulet\FluxOrders;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Nurdaulet\FluxOrders\Helpers\TextConverterHelper;
use Illuminate\Support\ServiceProvider;
use Nurdaulet\FluxOrders\Services\OrderLordFacadeService;

class FluxOrdersServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishConfig();
            $this->publishMigrations();
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/flux-orders.php',
            'flux-orders'
        );
        $this->app->bind('textConverter', TextConverterHelper::class);
        $this->app->bind('orders', OrderLordFacadeService::class);
//        $this->app->bind('stringFormatter', StringFormatterHelper::class);
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/flux-orders.php' => config_path('flux-orders.php'),
        ], 'flux-orders-config');


        if (!file_exists(config_path('flux-orders.php'))) {
            Artisan::call('vendor:publish', ['--tag' => 'flux-orders-config']);
        }
    }

    protected function publishMigrations()
    {

        $this->publishes([
            __DIR__ . '/../database/migrations/check_cancel_reasons_table.php.stub' => $this->getMigrationFileName('check_cancel_reasons_table.php'),
            __DIR__ . '/../database/migrations/check_payment_methods_table.php.stub' => $this->getMigrationFileName('check_payment_methods_table.php'),
            __DIR__ . '/../database/migrations/check_orders_table.php.stub' => $this->getMigrationFileName('check_orders_table.php'),
            __DIR__ . '/../database/migrations/check_canceled_orders_table.php.stub' => $this->getMigrationFileName('check_canceled_orders_table.php'),
            __DIR__ . '/../database/migrations/check_verify_issued_order.php.stub' => $this->getMigrationFileName('check_verify_issued_order.php'),
            __DIR__ . '/../database/migrations/check_verify_return_order.php.stub' => $this->getMigrationFileName('check_verify_return_order.php'),
        ], 'flux-base-migrations');
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     */
    protected function getMigrationFileName(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make([$this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR])
            ->flatMap(fn($path) => $filesystem->glob($path . '*_' . $migrationFileName))
            ->push($this->app->databasePath() . "/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}
