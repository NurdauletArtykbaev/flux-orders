# Пакет sms-kz для Laravel

Пакет flux-orders - заказы .

Установите пакет с помощью Composer:

``` bash
 composer require Nurdaulet/flux-orders
```

## Конфигурация

После установки пакета, вам нужно опубликовать конфигурационный файл. Вы можете сделать это с помощью следующей команды:

``` bash
php artisan vendor:publish --tag=flux-orders-config
php artisan vendor:publish --provider="Nurdaulet\FluxOrders\FluxOrdersServiceProvider"

```

Вы можете самостоятельно добавить поставщика услуг административной панели Filament в файл config/app.php.

``` php
'providers' => [
    // ...
    Nurdaulet\FluxOrders\FluxOrdersFilamentServiceProvider::class,
];
```

Вы можете самостоятельно добавить поставщика услуг административной панели Filament в файл config/app.php.

``` php
use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Nurdaulet\FluxAuth\Filament\Resources\UserResource;

public function boot(): void
{
    //...
    Filament::navigation(function (NavigationBuilder $builder): NavigationBuilder {
    return $builder
        ->groups([
            NavigationGroup::make('Главная')
                ->items([
                    ...UserResource::getNavigationItems(),
                    //...

                ]),
        ]);
    });
}
```



