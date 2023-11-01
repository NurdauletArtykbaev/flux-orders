<?php

declare(strict_types=1);

namespace Nurdaulet\FluxOrders\Http\Resources\LordOrder;

use Illuminate\Http\Resources\Json\JsonResource;
use Nurdaulet\FluxOrders\Http\Resources\ItemsResource;
use Nurdaulet\FluxOrders\Http\Resources\UserResource;
use Nurdaulet\FluxOrders\Http\Resources\RentTypeResource;
use Nurdaulet\FluxOrders\Http\Resources\ReceiveMethodsResource;

final class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'delivery_price' => $this->delivery_price,
            'delivery_date' => $this->delivery_date,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'comment' => $this->comment,
            'rent_price' => $this->rent_price,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'client_status' => $this->client_status,
            'lord_status' => $this->lord_status,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'rent_value' => $this->rent_value,
            'accepted_at' => $this->accepted_at,
            'delivery_time' => $this->delivery_time,
            'is_fast_delivery' => $this->is_fast_delivery,
            'is_used_bonus' => $this->is_used_bonus,
            'bonus' => $this->bonus,
            'phone' => $this->phone,
            'store_id' => $this->store_id,
            'item' => new ItemsResource($this->whenLoaded('item')),
            'user' => new UserResource($this->whenLoaded('user')),
            'rent_type' => new RentTypeResource($this->whenLoaded('rentType')),
            'receive_method' => new ReceiveMethodsResource($this->whenLoaded('receiveMethod')),
            'payment_method' => new ReceiveMethodsResource($this->whenLoaded('paymentMethod')),
        ];
    }
}
