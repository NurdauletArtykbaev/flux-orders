<?php

namespace Nurdaulet\FluxOrders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class OrderStoreContractRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'contract' => 'required|file|mimes:pdf,docx|max:2048',
        ];
    }
}
