@props([
    'disabled' => false,
    'for' => null,
    'id' => null,
    'size' => 'md'
])
@php
    //base class
    $baseClasses = 'block font-medium text-gray-900 mb-2';

    //size
    $sizes = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg',
    ];
@endphp

<label
    for="{{ $for }}"
    id="{{ $id }}"
    {{ $attributes->class([$baseClasses, $sizes[$size]]) }}
>
    {{ $slot }}
</label>