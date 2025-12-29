@props(['color' => null])

@php
    $themeColor = $color ?? team_theme()->primary();
@endphp

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => "inline-flex items-center px-4 py-2 bg-{$themeColor}-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-{$themeColor}-700 focus:bg-{$themeColor}-700 active:bg-{$themeColor}-800 focus:outline-none focus:ring-2 focus:ring-{$themeColor}-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150"
]) }}>
    {{ $slot }}
</button>
