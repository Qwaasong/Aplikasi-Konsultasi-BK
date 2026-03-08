@props(['disabled' => false,
        'size' => 'md'
])


@php
    // Class dasar 
    $baseClasses = 'w-full border border-gray-300 rounded-md
                    focus:outline-none focus:ring-1 focus:ring-brand-dark focus:border-brand-dark
                    transition duration-150';

    // Ukuran berbeda berdasarkan size
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
@endphp


<input
    @disabled($disabled)
    {{ $attributes->merge(['class' => "$baseClasses {$sizes[$size]}"]) }}
>