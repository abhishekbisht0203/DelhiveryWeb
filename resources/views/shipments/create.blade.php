@extends('layouts.app')
@section('title', 'Create Shipment')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('shipments.index') }}" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create Shipment</h1>
            <p class="text-sm text-gray-500 mt-1">Fill in the details to create a new shipment.</p>
        </div>
    </div>

    @include('components.flash-messages')

    <form method="POST" action="{{ route('shipments.store') }}" class="space-y-6">
        @csrf
        {{-- Order Details --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Order Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Order ID <span class="text-red-500">*</span></label>
                    <input type="text" name="order_id" value="{{ old('order_id') }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="ORD-12345">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Invoice Number</label>
                    <input type="text" name="invoice_number" value="{{ old('invoice_number') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="INV-12345">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Merchant <span class="text-red-500">*</span></label>
                    <select name="merchant_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Select Merchant</option>
                        <option value="1">Fashion Hub Pvt Ltd</option>
                        <option value="2">Tech Gadgets Online</option>
                        <option value="3">Organic Store</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Consignee Details --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Consignee Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="consignee_name" value="{{ old('consignee_name') }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Phone <span class="text-red-500">*</span></label>
                    <input type="tel" name="consignee_phone" value="{{ old('consignee_phone') }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Email</label>
                    <input type="email" name="consignee_email" value="{{ old('consignee_email') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Address Line 1 <span class="text-red-500">*</span></label>
                    <input type="text" name="address_line1" value="{{ old('address_line1') }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Address Line 2</label>
                    <input type="text" name="address_line2" value="{{ old('address_line2') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">City <span class="text-red-500">*</span></label>
                    <input type="text" name="city" value="{{ old('city') }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">State <span class="text-red-500">*</span></label>
                    <select name="state" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Select State</option>
                        <option value="MH">Maharashtra</option>
                        <option value="DL">Delhi</option>
                        <option value="KA">Karnataka</option>
                        <option value="TN">Tamil Nadu</option>
                        <option value="TS">Telangana</option>
                        <option value="GJ">Gujarat</option>
                        <option value="UP">Uttar Pradesh</option>
                        <option value="WB">West Bengal</option>
                        <option value="KL">Kerala</option>
                        <option value="RJ">Rajasthan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Pincode <span class="text-red-500">*</span></label>
                    <input type="text" name="pincode" value="{{ old('pincode') }}" required maxlength="6"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>

        {{-- Shipment Details --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Shipment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Weight (kg) <span class="text-red-500">*</span></label>
                    <input type="number" name="weight" value="{{ old('weight') }}" required step="0.1" min="0.1"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dimensions (L x W x H cm)</label>
                    <div class="flex gap-2">
                        <input type="number" name="length" value="{{ old('length') }}" placeholder="L" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <input type="number" name="width" value="{{ old('width') }}" placeholder="W" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <input type="number" name="height" value="{{ old('height') }}" placeholder="H" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Payment Type <span class="text-red-500">*</span></label>
                    <select name="payment_type" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="cod">COD</option>
                        <option value="prepaid">Prepaid</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">COD Amount</label>
                    <input type="number" name="cod_amount" value="{{ old('cod_amount') }}" step="0.01" min="0"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Declared Value</label>
                    <input type="number" name="declared_value" value="{{ old('declared_value') }}" step="0.01" min="0"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Pickup Hub <span class="text-red-500">*</span></label>
                    <select name="pickup_hub_id" required class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Select Hub</option>
                        <option value="1">Mumbai Hub</option>
                        <option value="2">Delhi Hub</option>
                        <option value="3">Bangalore Hub</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Delivery Partner</label>
                    <select name="delivery_partner_id" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Auto Assign</option>
                        <option value="1">Delhivery Express</option>
                        <option value="2">Delhivery Surface</option>
                        <option value="3">Delhivery Air</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Shipping Type</label>
                    <select name="shipping_type" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="standard">Standard</option>
                        <option value="express">Express</option>
                        <option value="surface">Surface</option>
                        <option value="air">Air</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Package contents description"></textarea>
            </div>
        </div>

        {{-- Pickup Details --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Pickup Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Pickup Date <span class="text-red-500">*</span></label>
                    <input type="date" name="pickup_date" value="{{ old('pickup_date', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Pickup Time Slot</label>
                    <select name="pickup_time_slot" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Any Time</option>
                        <option value="morning">Morning (9AM - 12PM)</option>
                        <option value="afternoon">Afternoon (12PM - 4PM)</option>
                        <option value="evening">Evening (4PM - 7PM)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Special Instructions</label>
                    <input type="text" name="special_instructions" value="{{ old('special_instructions') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g. Fragile item">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('shipments.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors">Create Shipment</button>
        </div>
    </form>
</div>
@endsection
