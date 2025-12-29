@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-stone-700']) }}>
    {{ $value ?? $slot }}
</label>
