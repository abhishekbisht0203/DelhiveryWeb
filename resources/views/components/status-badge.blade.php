@props(['status' => '', 'type' => ''])

@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
        'picked_up' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
        'in_transit' => 'bg-blue-100 text-blue-800 border-blue-200',
        'out_for_delivery' => 'bg-purple-100 text-purple-800 border-purple-200',
        'delivered' => 'bg-green-100 text-green-800 border-green-200',
        'failed' => 'bg-red-100 text-red-800 border-red-200',
        'returned' => 'bg-red-100 text-red-800 border-red-200',
        'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
        'ndr' => 'bg-orange-100 text-orange-800 border-orange-200',
        'rto' => 'bg-purple-100 text-purple-800 border-purple-200',
        'at_hub' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
        'pre_transit' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'open' => 'bg-orange-100 text-orange-800 border-orange-200',
        'resolved' => 'bg-green-100 text-green-800 border-green-200',
        'active' => 'bg-green-100 text-green-800 border-green-200',
        'inactive' => 'bg-gray-100 text-gray-800 border-gray-200',
    ];
    $color = $statusColors[strtolower(str_replace(' ', '_', $status))] ?? 'bg-gray-100 text-gray-800 border-gray-200';
    $label = ucwords(str_replace('_', ' ', $status));
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
    {{ $label }}
</span>
