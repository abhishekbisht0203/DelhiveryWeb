<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'merchant_id'              => 'required|exists:merchants,id',
            'order_id'                 => 'nullable|string|max:100',
            'invoice_number'           => 'nullable|string|max:100',
            'sender_name'              => 'required|string|max:255',
            'sender_phone'             => 'required|string|max:15',
            'sender_email'             => 'nullable|email|max:255',
            'sender_address'           => 'required|string|max:500',
            'sender_city'              => 'required|string|max:100',
            'sender_state'             => 'required|string|max:100',
            'sender_pincode'           => 'required|string|max:10',
            'receiver_name'            => 'required|string|max:255',
            'receiver_phone'           => 'required|string|max:15',
            'receiver_email'           => 'nullable|email|max:255',
            'receiver_address'         => 'required|string|max:500',
            'receiver_city'            => 'required|string|max:100',
            'receiver_state'           => 'required|string|max:100',
            'receiver_pincode'         => 'required|string|max:10',
            'receiver_landmark'        => 'nullable|string|max:255',
            'description'              => 'nullable|string|max:500',
            'quantity'                 => 'required|integer|min:1',
            'weight'                   => 'required|numeric|min:0.01',
            'length'                   => 'nullable|numeric|min:0',
            'width'                    => 'nullable|numeric|min:0',
            'height'                   => 'nullable|numeric|min:0',
            'declared_value'           => 'required|numeric|min:0',
            'cod_amount'               => 'nullable|numeric|min:0',
            'invoice_amount'           => 'nullable|numeric|min:0',
            'payment_mode'             => 'required|in:prepaid,cod,collectable',
            'origin_hub_id'            => 'nullable|exists:hubs,id',
            'destination_hub_id'       => 'nullable|exists:hubs,id',
            'pickup_scheduled_at'      => 'nullable|date|after:now',
            'expected_delivery_date'   => 'nullable|date|after_or_equal:today',
            'items'                    => 'nullable|array',
            'items.*.name'             => 'required_with:items|string|max:255',
            'items.*.quantity'         => 'required_with:items|integer|min:1',
            'items.*.value'            => 'required_with:items|numeric|min:0',
        ];
    }
}
