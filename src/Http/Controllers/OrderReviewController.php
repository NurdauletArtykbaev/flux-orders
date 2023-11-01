<?php

namespace Nurdaulet\FluxOrders\Http\Controllers;

use Nurdaulet\FluxOrders\Http\Requests\StoreOrderReviewRequest;
use Nurdaulet\FluxOrders\Http\Resources\OrdersResource;
use Nurdaulet\FluxOrders\Http\Resources\ReviewResource;
use Nurdaulet\FluxOrders\Repositories\OrderRepository;
use Nurdaulet\FluxBase\Services\ReviewService;
use Illuminate\Http\Request;

class OrderReviewController
{
    public function __construct(
        private ReviewService   $reviewService,
        private OrderRepository $orderRepository
    )
    {
    }

    public function store(StoreOrderReviewRequest $request, $id)
    {
        $user = $request->user();
        $order = $this->orderRepository->find($id);
        $review = $this->reviewService->create($user, $order, $request->validated());

        return response()->noContent();
    }

    public function skipReview(Request $request, $id)
    {
        $user = $request->user();
        $order = $this->orderRepository->find($id);
        $this->reviewService->skip($user, $order);
        return response()->noContent();
    }


    public function userNotReviewedAd(Request $request)
    {
        $order = $this->orderRepository->getByUserIdNotReviewedReview($request->user()->id);

        return $order ? new OrdersResource($order) : response()->json(['data' => null]);
    }
}
