<?php

namespace Nurdaulet\FluxOrders\Repositories;



class CancelReasonRepository
{
    public function get()
    {
        return config('flux-orders.models.cancel_reason')::select('id','name','description','type')
            ->active()
            ->get();
    }
}
