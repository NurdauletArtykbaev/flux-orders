<?php

namespace Nurdaulet\FluxOrders\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Nurdaulet\FluxItems\Helpers\ItemHelper;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'is_busy' => $this->is_busy,
            'user_id' => $this->user_id,
            'images' => ItemImageResource::collection($this->whenLoaded('images')),
            'type_raw' => $this->type ?? ItemHelper::TYPE_RENT,
            'type' => ItemHelper::TYPES[$this->type] ??  ItemHelper::TYPES[ItemHelper::TYPE_RENT],
        ];
    }
}
