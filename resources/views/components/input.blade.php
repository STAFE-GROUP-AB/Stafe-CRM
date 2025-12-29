@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-stone-300 bg-white text-stone-900 focus:border-emerald-500 focus:ring-emerald-500 rounded-md shadow-sm']) !!}>
