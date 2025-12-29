@props([
    'striped' => false,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden']) }}>
    {{ $slot }}
</div>
