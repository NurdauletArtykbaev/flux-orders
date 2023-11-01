<?php

namespace Nurdaulet\FluxOrders\Http\Controllers;

use Nurdaulet\FluxOrders\Http\Resources\LordOrder\OrdersResource;
use Nurdaulet\FluxOrders\Http\Resources\LordOrder\OrderResource;
use Nurdaulet\FluxOrders\Services\OrderLordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Nurdaulet\FluxOrders\Services\OrderService;

class OrderLordController 
{
    public function __construct(
        private OrderLordService $orderLordService,
        private OrderService $orderService
    )
    {
    }

    public function index(Request $request)
    {
        $orders = $this->orderLordService->getConfirmAcceptOrders($request->user());
        return OrdersResource::collection($orders);
    }
    public function show($id)
    {
        $order = $this->orderLordService->find($id);

        return new OrderResource($order);
//        $order->ad->user->contract_url = $order->ad->user->contract_url ?? null;
//        return $order;
    }
    public function acceptOrder($id, Request $request)
    {
        $this->orderLordService->updateAcceptOrder($request->user(), $id);

        return response()->noContent();
    }


    public function cancelOrder(Request $request, $id)
    {
        $this->orderLordService->cancelOrder($request->user(), $id, $request->get('cancel_id'));

        return response()->noContent();
    }


    public function issueOrder(Request $request, $id)
    {
        $this->orderLordService->issueOrderSendCode($request->user(), $id);
        return response()->noContent();
    }


    public function issueVerify($id, Request $request)
    {
        $user = $request->user();
        $this->orderLordService->verifyIssueCode($id, $user, $request->code);

        return response()->noContent();
    }


    public function returnOrder(Request $request, $id)
    {
        $this->orderLordService->returnOrderSendCode($request->user(), $id);

        return response()->noContent();
    }

    public function returnVerify(Request $request, $id)
    {
        $this->orderLordService->verifyReturnCode($request->user(),$request->get('code'), $id);

        return response()->noContent();
    }
}
