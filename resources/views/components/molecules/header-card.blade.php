@props(['title' => '', 'badge' => null, 'dateValue' => null, 'dateLabel' => null, 'height' => 'h-46'])

@php
    $attributes = $attributes->merge(['class' => 'w-full ' . $height]);
@endphp

<x-atoms.card {{ $attributes }}>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        {{-- Decorative Circle --}}
        <div
            class="absolute top-[0%] translate-y-[70%] translate-x-[250%] w-32 h-32 bg-white/30 blur-[20px] border border-white/10 rounded-full">
        </div>

        <div>
            @if(isset($badge))
                <span class="bg-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium">
                    {{ $badge }}
                </span>
            @endif
            <h2 class="text-2xl md:text-3xl font-bold mt-4 leading-tight">
                {{ $title }}
            </h2>
        </div>

        @if(isset($dateValue))
            <div class="text-left md:text-right">
                <p class="text-sm opacity-80 font-medium">{{ $dateLabel ?? 'Hari Ini' }}</p>
                <p class="text-xl md:text-2xl font-bold mt-1">{{ $dateValue }}</p>
            </div>
        @endif
    </div>

    {{ $slot }}
</x-atoms.card>