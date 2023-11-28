<?php

declare(strict_types=1);

namespace Nurdaulet\FluxOrders\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Nurdaulet\FluxOrders\Helpers\OrderHelper;

final class OrdersResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'type_raw' => $this->type ?? OrderHelper::TYPE_RENT,
            'type' => OrderHelper::TYPES[$this->type] ??  OrderHelper::TYPES[OrderHelper::TYPE_RENT],
            'created_at' => $this->created_at,
            'items' => ItemsResource::collection($this->whenLoaded('items')),
            'city' => new CityResource($this->whenLoaded('city')),
        ];
    }
}
