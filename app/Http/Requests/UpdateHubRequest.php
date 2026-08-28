<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHubRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'sometimes|string|max:255',
            'code'             => 'sometimes|string|max:20|unique:hubs,code,' . $this->route('hub')?->id,
            'address'          => 'sometimes|string|max:500',
            'city'             => 'sometimes|string|max:100',
            'state'            => 'sometimes|string|max:100',
            'pincode'          => 'sometimes|string|max:10',
            'phone'            => 'nullable|string|max:15',
            'email'            => 'nullable|email|max:255',
            'manager_name'     => 'nullable|string|max:255',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'capacity'         => 'nullable|integer|min:0',
            'status'           => 'nullable|in:active,inactive',
            'operating_hours'  => 'nullable|array',
        ];
    }
}
