<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id'                 => 'nullable|string|max:100',
            'invoice_number'           => 'nullable|string|max:100',
            'sender_name'              => 'sometimes|string|max:255',
            'sender_phone'             => 'sometimes|string|max:15',
            'sender_email'             => 'nullable|email|max:255',
            'sender_address'           => 'sometimes|string|max:500',
            'sender_city'              => 'sometimes|string|max:100',
            'sender_state'             => 'sometimes|string|max:100',
            'sender_pincode'           => 'sometimes|string|max:10',
            'receiver_name'            => 'sometimes|string|max:255',
            'receiver_phone'           => 'sometimes|string|max:15',
            'receiver_email'           => 'nullable|email|max:255',
            'receiver_address'         => 'sometimes|string|max:500',
            'receiver_city'            => 'sometimes|string|max:100',
            'receiver_state'           => 'sometimes|string|max:100',
            'receiver_pincode'         => 'sometimes|string|max:10',
            'receiver_landmark'        => 'nullable|string|max:255',
            'description'              => 'nullable|string|max:500',
            'quantity'                 => 'sometimes|integer|min:1',
            'weight'                   => 'sometimes|numeric|min:0.01',
            'length'                   => 'nullable|numeric|min:0',
            'width'                    => 'nullable|numeric|min:0',
            'height'                   => 'nullable|numeric|min:0',
            'declared_value'           => 'sometimes|numeric|min:0',
            'cod_amount'               => 'nullable|numeric|min:0',
            'invoice_amount'           => 'nullable|numeric|min:0',
            'payment_mode'             => 'sometimes|in:prepaid,cod,collectable',
            'origin_hub_id'            => 'nullable|exists:hubs,id',
            'destination_hub_id'       => 'nullable|exists:hubs,id',
            'delivery_partner_id'      => 'nullable|exists:delivery_partners,id',
            'pickup_scheduled_at'      => 'nullable|date',
            'expected_delivery_date'   => 'nullable|date',
        ];
    }
}
