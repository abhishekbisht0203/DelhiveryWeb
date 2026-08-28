<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        $settings = DB::table('settings')
            ->where('organization_id', auth()->user()->organization_id)
            ->pluck('value', 'key')
            ->toArray();

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name'                => 'nullable|string|max:255',
            'company_email'               => 'nullable|email|max:255',
            'company_phone'               => 'nullable|string|max:15',
            'company_address'             => 'nullable|string|max:500',
            'default_weight_unit'         => 'nullable|in:kg,lb',
            'volumetric_divisor'          => 'nullable|integer|min:1',
            'auto_assign_pickup'          => 'nullable|boolean',
            'auto_assign_delivery'        => 'nullable|boolean',
            'cod_enabled'                 => 'nullable|boolean',
            'cod_fee_percent'             => 'nullable|numeric|min:0|max:100',
            'max_cod_amount'              => 'nullable|numeric|min:0',
            'max_delivery_attempts'       => 'nullable|integer|min:1|max:10',
            'ndr_cooldown_hours'          => 'nullable|integer|min:1',
            'rto_fee'                     => 'nullable|numeric|min:0',
            'late_delivery_penalty'       => 'nullable|numeric|min:0',
            'sms_notifications_enabled'   => 'nullable|boolean',
            'email_notifications_enabled' => 'nullable|boolean',
            'webhook_url'                 => 'nullable|url|max:500',
            'api_rate_limit'              => 'nullable|integer|min:1',
        ]);

        $orgId = auth()->user()->organization_id;

        foreach ($validated as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key, 'organization_id' => $orgId],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
