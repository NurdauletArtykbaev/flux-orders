<?php

namespace Nurdaulet\FluxOrders\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'surname' => $this->surname,
            'phone' => $this->phone,
            'email' => $this->email,
            'last_name' => $this->last_name,
            'company_name' => $this->company_name,
            'avg_rating' => $this->avg_rating,
            'moderation_status' => $this->is_verified,
            'is_identified' => $this->is_identified,
        ];
    }
}
