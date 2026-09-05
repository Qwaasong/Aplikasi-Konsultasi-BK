@props([
    'data' => [],
])

@php
    $labels = collect($data)->pluck('label')->values()->toArray();
    $values = collect($data)->pluck('value')->values()->toArray();
@endphp

<div class="relative w-full h-[360px] sm:h-[380px] lg:h-[300px]">
    <canvas
        data-bar-chart
        data-labels='@json($labels)'
        data-values='@json($values)'
    ></canvas>
</div>