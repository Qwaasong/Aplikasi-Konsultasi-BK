@props([
    'color' => 'gray',
    'title' => ''
])
 
 <button

    title="{{ $title }}"
{{ $attributes->merge([
    'class' => "p-1.5 text-$color-500 hover:text-$color-600 hover:bg-$color-50 rounded-full transition-all duration-200"
]) }}
>
{{ $slot }}
</button>