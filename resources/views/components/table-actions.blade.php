@props([
    'id',
    'showView' => true,
    'showEdit' => true,
    'showDelete' => true,
])

@php
    $primaryColor = team_theme()->primary();
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center space-x-3']) }}>
    @if($showView)
        <button wire:click="show({{ $id }})" class="text-{{ $primaryColor }}-600 hover:text-{{ $primaryColor }}-900 dark:text-{{ $primaryColor }}-400 dark:hover:text-{{ $primaryColor }}-300 text-sm font-medium">
            {{ __('View') }}
        </button>
    @endif
    @if($showEdit)
        <button wire:click="edit({{ $id }})" class="text-stone-600 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-300 text-sm font-medium">
            {{ __('Edit') }}
        </button>
    @endif
    @if($showDelete)
        <button wire:click="confirmDelete({{ $id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
            {{ __('Delete') }}
        </button>
    @endif
    {{ $slot }}
</div>
