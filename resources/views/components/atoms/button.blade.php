@props([
    'variant' => 'primary',
    'size'    => 'md',
    'type'    => 'button',
])

@php
    // Class dasar 
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-md
                    focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed';

    // Warna berbeda berdasarkan variant
    $variants = [
        'primary'   => 'bg-brand-teal text-white border-b-2 border-brand-dark hover:bg-brand-dark',
        'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50',
        'danger'    => 'bg-red-600 text-white border-b-2 border-red-700 hover:bg-red-700',
        'ghost'     => 'bg-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-100',
    ];

    // Ukuran berbeda berdasarkan size
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "$baseClasses {$variants[$variant]} {$sizes[$size]}"]) }}
>
    {{ $slot }}
</button>