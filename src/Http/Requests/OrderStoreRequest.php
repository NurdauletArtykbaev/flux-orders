<?php

namespace Nurdaulet\FluxOrders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'bankcard_id' => 'nullable',
//            'receive_method_id' => 'required',
//            'delivery_date' => 'nullable',
//            'delivery_time' => 'nullable',
//            'delivery_price' => 'nullable',
//            'address' => 'nullable',
//            'lat' => 'nullable',
//            'lng' => 'nullable',
//            'comment' => 'nullable',
//            'is_fast_delivery' => 'nullable',
//            'is_used_bonus' => 'nullable',
        ];
    }
}
