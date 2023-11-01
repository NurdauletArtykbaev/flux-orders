<?php

namespace Nurdaulet\FluxOrders\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemImageResource extends JsonResource
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
            'full_url' => $this->full_url,
            'webp_full_url' => $this->webp_full_url,
        ];
    }
}
