@props([
    'actions' => [],
    'selectedCount' => 0,
    'bulkAction' => '',
])

@php
    $primaryColor = team_theme()->primary();
@endphp

@if($selectedCount > 0)
<div {{ $attributes->merge(['class' => 'flex items-center space-x-2']) }}>
    <span class="text-sm text-gray-600 dark:text-gray-400">
        {{ $selectedCount }} {{ __('selected') }}
    </span>
    <select wire:model.live="bulkAction" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-{{ $primaryColor }}-500 focus:ring-{{ $primaryColor }}-500 sm:text-sm">
        <option value="">{{ __('Bulk Actions') }}</option>
        @foreach($actions as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
    @if($bulkAction)
        <button wire:click="executeBulkAction" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-{{ $primaryColor }}-600 hover:bg-{{ $primaryColor }}-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $primaryColor }}-500 dark:focus:ring-offset-gray-800">
            {{ __('Apply') }}
        </button>
    @endif
</div>
@endif
