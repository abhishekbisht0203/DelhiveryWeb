<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHubRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'code'             => 'required|string|max:20|unique:hubs,code',
            'address'          => 'required|string|max:500',
            'city'             => 'required|string|max:100',
            'state'            => 'required|string|max:100',
            'pincode'          => 'required|string|max:10',
            'phone'            => 'nullable|string|max:15',
            'email'            => 'nullable|email|max:255',
            'manager_name'     => 'nullable|string|max:255',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'capacity'         => 'nullable|integer|min:0',
            'status'           => 'nullable|in:active,inactive',
            'operating_hours'  => 'nullable|array',
            'operating_hours.open'  => 'required_with:operating_hours|string',
            'operating_hours.close' => 'required_with:operating_hours|string',
        ];
    }
}
