@props([
    'label',
    'value',
    'icon',
    'color' => 'emerald'
])

@php
    $gradients = [
        'emerald' => 'bg-[linear-gradient(310deg,#86A280_0%,#000000_100%)]',
        'ruby' => 'bg-[linear-gradient(310deg,#E33155_0%,#000000_100%)]',
        'purple' => 'bg-[linear-gradient(310deg,#9780A2_0%,#000000_100%)]',
        'yellow' => 'bg-[linear-gradient(310deg,#D4E331_0%,#000000_100%)]',
        'green' => 'bg-[linear-gradient(310deg,#31E36C_0%,#000000_100%)]',
    ];
    $bgClass = $gradients[$color] ?? $gradients['emerald'];
@endphp

<x-atoms.card :bg="$bgClass" {{ $attributes->merge(['class' => 'flex flex-col justify-between p-6 rounded-[1.5rem]']) }}>
    <div class="relative z-10">
        <div class="bg-white/10 rounded-xl aspect-square flex items-center justify-center w-12 h-12 backdrop-blur-lg border ">
            {{ $icon }}
        </div>
        
        <div class="mt-6">
            <p class="text-xs font-semibold opacity-90 uppercase tracking-wider">{{ $label }}</p>
            <h3 class="text-3xl font-bold mt-2">{{ $value }}</h3>
        </div>
    </div>
    
    {{-- Decorative Circle --}}
    <div class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-1/2 w-32 h-32 bg-white/20 backdrop-blur-lg border border-white/100 rounded-full"></div>
</x-atoms.card>
