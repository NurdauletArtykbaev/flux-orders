<?php

declare(strict_types=1);

namespace Nurdaulet\FluxOrders\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
