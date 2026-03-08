@props([
    'value',
    'label',
    'size' => 'md'
])

@php

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

@endphp

<li
    @click="
        value='{{ $value }}';
        label='{{ $label }}';
        open=false
    "
    class="{{ $sizes[$size] }} hover:bg-gray-100 cursor-pointer"
>
{{ $label }}
</li>