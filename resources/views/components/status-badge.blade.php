@props([
    'status' => 'default',
    'color' => null,
])

@php
    $colors = [
        'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'inactive' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'lead' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'customer' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
        'prospect' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'churned' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'open' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'won' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'lost' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'high' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'low' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    ];

    $colorClass = $color ?? ($colors[strtolower($status)] ?? $colors['default']);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $colorClass]) }}>
    {{ $slot->isEmpty() ? ucfirst($status) : $slot }}
</span>
