<?php

namespace Nurdaulet\FluxOrders\Http\Controllers;

use Nurdaulet\FluxOrders\Http\Resources\CancelReasonsResource;
use Nurdaulet\FluxOrders\Repositories\CancelReasonRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CancelReasonController
{
    public function __construct(private CancelReasonRepository $cancelReasonRepository)
    {
    }

    public function __invoke(Request $request)
    {
        return Cache::remember("cancel-reasons", 269746, function () {
            return CancelReasonsResource::collection($this->cancelReasonRepository->get());
        });
    }
}
