<?php

namespace Nurdaulet\FluxOrders\Http\Controllers;

use Nurdaulet\FluxOrders\Http\Requests\OrderStoreRequest;
//use Nurdaulet\FluxOrders\Http\Resources\OrderResource;
use Nurdaulet\FluxOrders\Http\Resources\LordOrder\OrderResource;
use Nurdaulet\FluxOrders\Http\Resources\OrdersResource;
use Nurdaulet\FluxOrders\Models\Order;
use Nurdaulet\FluxOrders\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController
{
    public function __construct(
        private OrderService    $orderService,
    )
    {
    }

    public function store(OrderStoreRequest $request)
    {
        $user = $request->user();
        $order = $this->orderService->create($user, $request);

        return response()->noContent();
    }


    public function index(Request $request)
    {
        $orders = $this->orderService->getByUser($request->user(), $request->input('filters' ,[]));

        return OrdersResource::collection($orders);
    }

    public function show($id)
    {
        $order = $this->orderService->show($id);

        return new OrderResource($order);
    }

    public function cancelByUser(Request $request, $id)
    {
        $this->orderService->cancelByUser($request->user(), $id, $request->get('cancel_id'));

        return response()->noContent();
    }

//    public function orderContract($orderId): JsonResponse
//    {
//        return $this->orderService->downloadOrderContract($orderId);
//    }


//    public function orderContractSeller($orderId): JsonResponse
//    {
//        return response()
//            ->json(['data' => ['contract' => $this->orderService->getSellerContract($orderId)]]);
//    }
}
