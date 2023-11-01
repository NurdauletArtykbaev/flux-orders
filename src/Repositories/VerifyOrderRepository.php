<?php

namespace Nurdaulet\FluxOrders\Repositories;

class VerifyOrderRepository
{
    public function updateIssueOrder($filters = [], $data = [])
    {

        return config('flux-orders.models.verify_issued_order')::updateOrCreate(
            $filters,
            $data,
        );;
    }

    public function firstIssueOrderCode($orderId)
    {

        return config('flux-orders.models.verify_issued_order')::where('order_id', $orderId)->latest()->first()?->code;
    }

    public function firstReturnOrderCode($orderId)
    {


        return config('flux-orders.models.verify_return_order')::where('order_id', $orderId)->latest()->first()?->code;
    }

    public function updateReturnOrder($filters = [], $data = [])
    {

        return config('flux-orders.models.verify_return_order')::updateOrCreate(
            $filters,
            $data,
        );;
    }
}
