@props(['title' => '', 'value' => '', 'icon' => '', 'color' => 'primary', 'change' => '', 'changeType' => ''])

@php
    $colorClasses = [
        'primary' => 'bg-primary-50 text-primary-600',
        'success' => 'bg-green-50 text-green-600',
        'warning' => 'bg-amber-50 text-amber-600',
        'danger' => 'bg-red-50 text-red-600',
        'purple' => 'bg-purple-50 text-purple-600',
        'blue' => 'bg-blue-50 text-blue-600',
    ];
@endphp

<div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $colorClasses[$color] ?? $colorClasses['primary'] }}">
                <span class="text-lg">{!! $icon !!}</span>
            </div>
        @endif
    </div>
    @if($change)
        <div class="mt-3 flex items-center gap-1">
            @if($changeType === 'up')
                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
            @elseif($changeType === 'down')
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            @endif
            <span class="text-xs font-medium {{ $changeType === 'up' ? 'text-green-600' : ($changeType === 'down' ? 'text-red-600' : 'text-gray-500') }}">{{ $change }}</span>
        </div>
    @endif
</div>
