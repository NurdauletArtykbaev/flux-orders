
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
php artisan vendor:publish --provider="Nurdaulet\FluxOrders\FluxOrderssServiceProvider"

```



