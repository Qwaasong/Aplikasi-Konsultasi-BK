@props([
    'label',
    'value',
    'icon',
    'url' => '#',
    'color' => 'emerald'
])

@php
    $gradients = [
        'emerald' => 'bg-white',
        'ruby' => 'bg-white',
        'purple' => 'bg-white',
        'yellow' => 'bg-white',
        'green' => 'bg-white',
    ];
    $bgClass = $gradients[$color] ?? $gradients['emerald'];
@endphp

<x-atoms.card :bg="$bgClass" {{ $attributes->merge(['class' => 'relative flex flex-col justify-between rounded-[1rem] border-2 border-[#086375] overflow-hidden group']) }}>
    <div class="relative z-10 ">
        <div class="bg-[#086375] rounded-xl aspect-square flex items-center justify-center w-12 h-12 backdrop-blur-lg border">
            {{ $icon }}
        </div>

        <div class="mt-6 text-[#086375]">
            <p class="text-xs font-semibold opacity-90 uppercase tracking-wider">{{ $label }}</p>
            <h3 class="text-3xl font-bold mt-2">{{ $value }}</h3>
        </div>

        {{-- Tambahan Tombol Redirect --}}
        <div class="mt-6">
            <a href="{{ $url }}" class="flex items-center justify-between bg-gray-100 rounded-lg px-4 py-2 text-[#086375] transition-all hover:bg-gray-200">
                <span class="text-[10px] font-bold uppercase">{{ $label }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </div>

    {{-- Decorative Circle --}}
    <div class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-1/2 w-32 h-32 bg-[#e0f7fa]/20 backdrop-blur-lg border border-[#086375]/10 rounded-full -z-0"></div>
</x-atoms.card>
