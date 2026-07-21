@props([
    'menu',
    'depth' => 1,
])

<div
    x-data="{ openDropdown: {{ $menu['active'] ? 'true' : 'false' }} }"
    class="w-full">

    {{-- Parent --}}
    <x-molecules.sidebar-button
        :variants="$menu['variants']"
        :active="$menu['active']"
        :iconSize="$depth == 1 ? 'lg' : 'md'"
        @click="openDropdown = !openDropdown">

        {{ $menu['label'] }}

    </x-molecules.sidebar-button>

    {{-- Children --}}
    <div
        x-show="openDropdown && open"
        x-transition
        class="overflow-hidden">

        <div class="mt-1 space-y-1">

            @foreach($menu['children'] as $child)

                @if(!empty($child['children']))

                    <div class="{{ $depth == 1 ? 'pl-6' : 'pl-10' }}">

                        <x-molecules.sidebar-dropdown
                            :menu="$child"
                            :depth="$depth" />

                    </div>

                @else

                    <x-molecules.sidebar-item
                        :linkHref="$child['url']"
                        :active="$child['active']"
                        :variants="$child['variants']"
                        :iconSize="$depth == 1 ? 'md' : 'sm'"
                        :showIcon="$depth == 1"
                        :indent="$depth == 1 ? 'pl-6' : 'pl-10'">

                        {{ $child['label'] }}

                    </x-molecules.sidebar-item>

                @endif

            @endforeach

        </div>

    </div>

</div>