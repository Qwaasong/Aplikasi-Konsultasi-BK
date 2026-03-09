@props([
    'disabled' => false,
    'size' => 'md',
    'options' => [],
    'placeholder' => 'Pilih opsi'
])

@php

$baseClasses = 'w-full border border-gray-300 rounded-md
focus:outline-none focus:ring-1 focus:ring-brand-dark focus:border-brand-dark
transition duration-150 bg-white cursor-pointer flex items-center justify-between';

$sizes = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
];

@endphp

<div
    x-data="{
        open:false,
        value:null,
        label:'{{ $placeholder }}'
    }"
    class="relative w-full"
>

    <!-- Input -->
    <div
        @click="open=!open"
        class="{{ $baseClasses }} {{ $sizes[$size] }}"
    >

        <span
            x-text="label"
            class="text-gray-500"
        ></span>

        <x-atoms.icon
            variant="chevron"
            x-bind:class="{ 'rotate-180': open }"
        />

    </div>


    <!-- Dropdown -->
    <div
        x-show="open"
        @click.outside="open=false"
        x-transition
        class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-md shadow"
    >

        <ul class="py-1">

            @foreach($options as $option)

                @if($option === 'divider')

                    <x-atoms.dropdown-divider />

                @else

                    <x-atoms.dropdown-item
                        :value="$option['value']"
                        :label="$option['label']"
                        :size="$size"
                    />

                @endif

            @endforeach

        </ul>

    </div>


    <x-atoms.input-hidden
        x-model="value"
        {{ $attributes }}
    />

</div>