<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMerchantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name'           => 'required|string|max:255',
            'owner_name'              => 'required|string|max:255',
            'phone'                   => 'required|string|max:15',
            'email'                   => 'required|email|max:255|unique:merchants,email',
            'gst_number'              => 'nullable|string|max:20',
            'pan_number'              => 'nullable|string|max:20',
            'billing_address'         => 'nullable|string|max:500',
            'billing_city'            => 'nullable|string|max:100',
            'billing_state'           => 'nullable|string|max:100',
            'billing_pincode'         => 'nullable|string|max:10',
            'cod_enabled'             => 'boolean',
            'cod_fee_percent'         => 'nullable|numeric|min:0|max:100',
            'max_cod_amount'          => 'nullable|numeric|min:0',
            'monthly_shipment_limit'  => 'nullable|integer|min:0',
            'pricing_tier'            => 'nullable|string|max:50',
            'status'                  => 'nullable|in:active,inactive,suspended',
            'notes'                   => 'nullable|string|max:1000',
        ];
    }
}
