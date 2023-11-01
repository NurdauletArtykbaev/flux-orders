<?php

namespace Nurdaulet\FluxOrders\Repositories;


class CanceledOrderRepository
{
    public function create($orderId, $data)
    {
        return config('flux-orders.models.canceled_order')::firstOrCreate(
            [
                'order_id' => $orderId
            ], $data,
        );
    }

}
