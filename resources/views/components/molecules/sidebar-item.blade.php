@props([
'linkHref' => '#',
'active' => false,
'variants' => 'dashboard',
'iconSize' => 'lg',
'showIcon' => true,
'indent' => '',
])

<a
    href="{{ $linkHref }}"
    wire:navigate
    @class([ 'group/menu flex items-center h-12 w-full px-4' , 'text-gray-600'=> !$active
    ])>

    <div @class([ 'flex items-center rounded-lg transition-all duration-300 w-full h-12 pr-4' , $indent , 'bg-brand-teal-light'=> $active,
        'group-hover/menu:bg-gray-100' => !$active
        ])>

        {{-- Icon --}}
        @if($showIcon)
        <div @class([ 'w-10 h-12 flex-shrink-0 flex justify-center items-center' , 'text-brand-teal'=> $active,
            'group-hover/menu:text-brand-teal transition-colors' => !$active
            ])>

            <x-atoms.icon
                :variant="$variants"
                :size="$iconSize" />

        </div>
        @endif

        {{-- Label --}}
        <div
            x-show="open"
            x-transition.opacity
            class="flex-1">

            <x-atoms.nav-label :active="$active">
                {{ $slot }}
            </x-atoms.nav-label>

        </div>

    </div>

</a>