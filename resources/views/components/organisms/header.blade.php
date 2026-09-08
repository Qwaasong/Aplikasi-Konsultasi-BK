<header
    class="
        w-full
        min-h-20
        border-b border-gray-200
        bg-white
        shrink-0
        flex
        flex-col
        sm:flex-row
        sm:items-center
        sm:justify-between
        gap-3
        px-4
        sm:px-6
        lg:px-8
        py-3
        sm:py-0
    "
>

    {{-- SEARCH --}}
    <div class="w-full sm:flex-1 sm:min-w-0">
        {{ $search }}
    </div>

    {{-- TITLE / ACTION --}}
    <div
        class="
            w-full
            sm:w-auto
            flex
            items-center
            gap-2
            sm:justify-end
        "
    >

        @if(!empty($action))

            <x-atoms.button
                wire:click="{{ $action }}"
                class="
                    w-full
                    sm:w-auto
                    bg-brand-teal
                    hover:bg-brand-dark-3
                    text-white
                    px-4
                    sm:px-6
                    py-2.5
                    sm:py-3
                    rounded-lg
                    flex
                    items-center
                    justify-center
                    font-medium
                    gap-2
                    shadow-sm
                    transition
                    whitespace-nowrap
                "
            >
                <x-atoms.icon variant="plus" size="md" />

                <span>
                    {{ $slot }}
                </span>

            </x-atoms.button>

        @else

            <h2
                class="
                    font-semibold
                    text-lg
                    sm:text-xl
                    text-gray-800
                    leading-tight
                    w-full
                    sm:w-auto
                "
            >
                {{ $slot }}
            </h2>

        @endif

    </div>

</header>