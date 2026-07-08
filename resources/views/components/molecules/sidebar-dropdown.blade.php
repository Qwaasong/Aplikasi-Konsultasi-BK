@props([
'menu',
'nested' => false,
])

<div
    x-data="{ openDropdown: {{ $menu['active'] ? 'true' : 'false' }} }"
    class="w-full">

    {{-- Parent --}}
    <x-molecules.sidebar-button
        :variants="$menu['variants']"
        :active="$menu['active']"
        :iconSize="$nested ? 'md' : 'lg'"
        @click="openDropdown = !openDropdown">

        {{ $menu['label'] }}

    </x-molecules.sidebar-button>

    {{-- Children --}}
    <div
        x-show="openDropdown && open"
        x-transition
        class="overflow-hidden">

        <div class="mt-1 ml-6 space-y-1">

            @foreach($menu['children'] as $child)

            @if(!empty($child['children']))

            <x-molecules.sidebar-dropdown
                :menu="$child"
                nested />

            @else

            <x-molecules.sidebar-item
                :linkHref="$child['url']"
                :active="$child['active']"
                :variants="$child['variants']"
                :iconSize="$nested ? 'sm' : 'md'"
                :showIcon="!$nested"
                :indent="$nested ? 'pl-8' : ''">

                {{ $child['label'] }}

            </x-molecules.sidebar-item>

            @endif

            @endforeach

        </div>

    </div>

</div>