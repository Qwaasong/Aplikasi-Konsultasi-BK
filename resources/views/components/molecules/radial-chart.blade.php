@props([
'data' => [],
])

@php
$labels = collect($data)->pluck('label')->values()->toArray();
$values = collect($data)->pluck('value')->values()->toArray();

$total = array_sum($values);

$percentages = collect($values)
->map(fn ($value) => $total > 0 ? round(($value / $total) * 100, 1) : 0)
->values()
->toArray();

$colors = [
'#086375',
'#4CAF50',
'#E0A800',
];
@endphp

<div class="w-full">

    <div class="relative w-full h-[280px] max-w-[380px] mx-auto">
        <canvas
            data-radial-chart
            data-labels='@json($labels)'
            data-values='@json($values)'></canvas>
    </div>

    <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 mt-4">

        @foreach ($labels as $index => $label)
        <div class="flex items-center gap-2 text-sm text-gray-600">

            <span
                class="w-3 h-3 rounded-sm shrink-0"
                style="background-color: {{ $colors[$index] ?? '#086375' }}"></span>

            <span>
                {{ $label }}
            </span>

            <span class="font-semibold text-[#086375]">
                {{ $percentages[$index] }}%
            </span>

        </div>
        @endforeach

    </div>

</div>