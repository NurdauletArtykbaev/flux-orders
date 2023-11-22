<?php

namespace Nurdaulet\FluxOrders\Repositories;


use Nurdaulet\FluxOrders\Filters\OrderFilter;
use Nurdaulet\FluxOrders\Helpers\OrderHelper;

class OrderRepository
{
    public function find($id, $relations = [], $filters = [])
    {
        return config('flux-orders.models.order')::with($relations)
            ->applyFilters(new OrderFilter(), $filters)
            ->withTrashed()
            ->findOrFail($id);
    }

    public function getByUserIdNotReviewedReview($userId)
    {
        return config('flux-orders.models.order')::with('item')
            ->whereNotNull('item_id')
            ->applyFilters(new OrderFilter(), [
                'status' => [OrderHelper::STATUS_ACTIVE, OrderHelper::STATUS_FINISHED],
                'user_id' => $userId
            ])
            ->whereDoesntHave('reviews')
            ->first();
    }

    public function count($filters = [])
    {
        return config('flux-orders.models.order')::applyFilters(new OrderFilter(), $filters)
            ->count();
    }

    public function create($data)
    {
        return config('flux-orders.models.order')::create($data);
    }

    public function update($filters = [], $data = [])
    {
        return config('flux-orders.models.order')::applyFilters(new OrderFilter(),$filters)
            ->update($data);
    }

    public function paginate($filters = [],$relations = ['item', 'rentType'])
    {
        return config('flux-orders.models.order')::with($relations)
            ->applyFilters(new OrderFilter(), $filters)
            ->paginate(request()->input('per_page', 20));
    }
}
