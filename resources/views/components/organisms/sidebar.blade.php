@props([
    'menus' => []
])

<aside class="group w-20 hover:w-64 transition-all duration-300 ease-in-out bg-white border-r border-gray-200 flex flex-col justify-between z-20 shadow-lg absolute h-full md:relative flex-shrink-0">

    <div class="flex flex-col w-full">

        <div class="h-20 flex items-center overflow-hidden whitespace-nowrap">
            <x-atoms.application-logo class="w-10 h-10 object-contain"/>
        </div>

        <nav class="flex flex-col mt-4 w-full space-y-2">

            @foreach($menus as $menu)

                <x-molecules.sidebar-item
                    :active="$menu['active']"
                    :variants="$menu['variants']"
                >
                    {{ $menu['label'] }}

                </x-molecules.sidebar-item>

            @endforeach

        </nav>

    </div>

    {{ $footer }}

</aside>