<?php

namespace Nurdaulet\FluxOrders\Facades;

use Illuminate\Support\Facades\Facade;

class OrdersFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'orders';
    }
}
