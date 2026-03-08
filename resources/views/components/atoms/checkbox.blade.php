@props(['disabled' => false
])


@php
    // Class dasar 
    $baseClasses = 'h-4 w-4 border border-gray-300 rounded text-brand-teal cursor-pointer';
@endphp


<input
    type="checkbox"
    @disabled($disabled)
    {{ $attributes->merge(['class' => $baseClasses]) }}
>