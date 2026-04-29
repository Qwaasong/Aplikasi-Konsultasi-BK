@props([
    'menus' => [],
])

<aside x-data="{
    open: localStorage.getItem('sidebar') === 'true',
    ready: false
}" x-init="$nextTick(() => ready = true)" @mouseenter="open = true; localStorage.setItem('sidebar', 'true')"
    @mouseleave="open = false; localStorage.setItem('sidebar', 'false')"
    :class="[open ? 'w-64' : 'w-20', ready ? 'transition-all duration-200 ease-out' : '']"
    class="group bg-white border-r border-gray-200 flex flex-col justify-between z-20 shadow-lg absolute h-full md:relative flex-shrink-0">

    <div class="flex flex-col w-full">

        <div class="h-20 flex items-center overflow-hidden whitespace-nowrap">
            <x-atoms.application-logo class="w-10 h-10 object-contain" />
        </div>

        <nav class="flex flex-col mt-4 w-full space-y-2">
            @foreach ($menus as $menu)
                <x-molecules.sidebar-item :linkHref="$menu['url'] ?? '#'" :active="$menu['active']" :variants="$menu['variants']">

                    <span :class="open ? 'opacity-100 w-auto ml-3' : 'opacity-0 w-0 overflow-hidden'"
                        class="transition-all duration-200 whitespace-nowrap block">
                        {{ $menu['label'] }}
                    </span>

                </x-molecules.sidebar-item>
            @endforeach
        </nav>

    </div>

    {{ $footer }}

</aside>
