<?php

declare(strict_types=1);

namespace Nurdaulet\FluxOrders\Http\Resources\LordOrder;

use Illuminate\Http\Resources\Json\JsonResource;
use Nurdaulet\FluxOrders\Http\Resources\ItemResource;
use Nurdaulet\FluxOrders\Http\Resources\CityResource;
use Nurdaulet\FluxOrders\Http\Resources\RentTypeResource;

final class OrdersResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'rent_price' => $this->rent_price,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'rent_type' => new RentTypeResource($this->whenLoaded('rentType')),
            'rent_value' => $this->rent_value,
            'item' => new ItemResource($this->whenLoaded('item')),
            'city' => new CityResource($this->whenLoaded('city')),
        ];
    }
}
