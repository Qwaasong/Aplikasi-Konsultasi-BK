@props([
    'title',
    'subtitle',
    'description',
    'details' => [],
    'route' => '#',
    'button' => 'Mulai',
    'variants' => 'dashboard',
    'color' => 'blue',
])

@php
    $colors = [
        'blue' => [
            'bg' => 'bg-blue-100',
            'text' => 'text-blue-600',
            'button' => 'bg-blue-700 hover:bg-blue-800',
        ],
        'green' => [
            'bg' => 'bg-green-100',
            'text' => 'text-green-600',
            'button' => 'bg-green-700 hover:bg-green-800',
        ],
        'purple' => [
            'bg' => 'bg-purple-100',
            'text' => 'text-purple-600',
            'button' => 'bg-purple-700 hover:bg-purple-800',
        ],
        'orange' => [
            'bg' => 'bg-orange-100',
            'text' => 'text-orange-600',
            'button' => 'bg-orange-700 hover:bg-orange-800',
        ],
        'teal' => [
            'bg' => 'bg-brand-teal-light',
            'text' => 'text-brand-teal',
            'button' => 'bg-brand-teal hover:bg-brand-dark',
        ],
    ];

    $theme = $colors[$color] ?? $colors['blue'];
@endphp

<div
    class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition duration-200 overflow-hidden">

    <div class="p-6 flex flex-col md:flex-row gap-6">

        {{-- Icon --}}
        <div class="w-28 h-28 rounded-xl {{ $theme['bg'] }} flex items-center justify-center shrink-0">

            <div class="{{ $theme['text'] }}">
                <x-atoms.icon
                    :variant="$variants"
                    size="2xl" />
            </div>

        </div>

        {{-- Content --}}
        <div class="flex-1">

            <h2 class="text-2xl font-bold text-gray-900">
                {{ $title }}
            </h2>

            <p class="text-gray-700 font-semibold mt-1">
                {{ $subtitle }}
            </p>

            <p class="text-gray-600 leading-7 mt-4">
                {{ $description }}
            </p>

            @if(count($details))
                <div class="mt-6">

                    <h3 class="font-semibold text-gray-900 mb-2">
                        Detail Penting
                    </h3>

                    <ul class="space-y-1 list-disc ml-5 text-gray-600">

                        @foreach($details as $detail)
                            <li>{{ $detail }}</li>
                        @endforeach

                    </ul>

                </div>
            @endif

            <div class="mt-8">

                <a
                    href="{{ $route }}"
                    class="inline-flex items-center justify-center w-full md:w-auto px-8 py-3 rounded-lg text-white font-semibold transition {{ $theme['button'] }}">

                    {{ $button }}

                </a>

            </div>

        </div>

    </div>

</div>