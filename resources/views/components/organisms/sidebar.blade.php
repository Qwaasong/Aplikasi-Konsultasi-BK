@props([
'menus' => [],
'title' => '',
])

@php
$title = match(auth()->user()->role) {
        'admin' => 'Halaman Admin',
        'guru_bk' => 'Halaman Konselor',
        default => 'Dashboard',
    };
@endphp

<aside x-data="{
    open: localStorage.getItem('sidebar') === 'true',
    ready: false
}" x-init="$nextTick(() => ready = true)" @mouseenter="open = true; localStorage.setItem('sidebar', 'true')"
    @mouseleave="open = false; localStorage.setItem('sidebar', 'false')"
    :class="[open ? 'w-64' : 'w-20', ready ? 'transition-all duration-200 ease-out' : '']"
    class="group bg-white border-r border-gray-200 flex flex-col justify-between z-20 shadow-lg absolute h-full md:relative flex-shrink-0">

    <div class="flex flex-col flex-1 min-h-0 w-full">

        {{-- Logo --}}
        <div class="h-20 flex items-center overflow-hidden whitespace-nowrap flex-shrink-0">
            <x-atoms.application-logo class="w-10 h-10 object-contain" />
            <span x-show="open" x-transition.opacity.duration.200ms class="font-bold text-lg text-gray-900 -ml-1">
                @if(Auth::check())
                    @if(Auth::user()->role === 'admin')
                        Halaman Admin
                    @elseif(Auth::user()->role === 'guru_bk')
                        Halaman Konselor
                    @endif
                @endif
            </span>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden mt-4 w-full space-y-2">

            @foreach ($menus as $menu)

            @if (!empty($menu['children']))

            <x-molecules.sidebar-dropdown :menu="$menu" />

            @else

            <x-molecules.sidebar-item
                :linkHref="$menu['url'] ?? '#'"
                :active="$menu['active']"
                :variants="$menu['variants']">

                {{ $menu['label'] }}

            </x-molecules.sidebar-item>

            @endif

            @endforeach

        </nav>

    </div>

    {{ $footer }}

</aside>