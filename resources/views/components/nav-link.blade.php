@props(['active'])

@php
    $primaryColor = team_theme()->primary();
    $classes = ($active ?? false)
                ? "inline-flex items-center px-1 pt-1 border-b-2 border-{$primaryColor}-400 dark:border-{$primaryColor}-500 text-sm font-semibold leading-5 text-stone-900 dark:text-gray-100 focus:outline-none focus:border-{$primaryColor}-700 transition duration-150 ease-in-out"
                : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-semibold leading-5 text-stone-600 dark:text-gray-400 hover:text-stone-900 dark:hover:text-gray-300 hover:border-stone-300 dark:hover:border-gray-700 focus:outline-none focus:text-stone-900 dark:focus:text-gray-300 focus:border-stone-300 dark:focus:border-gray-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
