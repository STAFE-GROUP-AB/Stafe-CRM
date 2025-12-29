@props([
    'columns' => 6,
])

<div {{ $attributes->merge(['class' => 'p-4 border-b border-gray-200 dark:border-gray-700']) }}>
    <div class="grid grid-cols-1 md:grid-cols-{{ $columns }} gap-4">
        {{ $slot }}
    </div>
</div>
