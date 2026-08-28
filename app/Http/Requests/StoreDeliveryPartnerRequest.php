<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryPartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hub_id'          => 'required|exists:hubs,id',
            'name'            => 'required|string|max:255',
            'phone'           => 'required|string|max:15|unique:delivery_partners,phone',
            'email'           => 'nullable|email|max:255|unique:delivery_partners,email',
            'vehicle_type'    => 'required|in:bicycle,motorcycle,van,truck,other',
            'vehicle_number'  => 'nullable|string|max:20',
            'license_number'  => 'nullable|string|max:20',
            'aadhar_number'   => 'nullable|string|max:12',
            'assigned_areas'  => 'nullable|array',
            'assigned_areas.*' => 'string|max:10',
            'status'          => 'nullable|in:active,inactive,on_leave',
        ];
    }
}
