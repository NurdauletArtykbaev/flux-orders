<?php

namespace Nurdaulet\FluxOrders\Http\Controllers;

use Nurdaulet\FluxOrders\Http\Resources\PaymentMethodsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PaymentMethodController
{
    public function __invoke(Request $request)
    {
        $lang = app()->getLocale();

        return Cache::remember("payment-methods-news-$lang", 269746, function () {
            return PaymentMethodsResource::collection(config('flux-orders.models.payment_method')::active()->get());
        });

    }
}
