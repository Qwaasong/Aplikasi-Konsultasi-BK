@props([
    'onFilter' => null,
    'onRefresh' => null
])

<div
    class="
        min-h-14
        border-b border-gray-200
        bg-white
        shrink-0
        flex
        flex-wrap
        items-center
        justify-between
        gap-2
        px-3
        sm:px-4
    "
>

    {{-- LEFT SIDE --}}
    <div class="flex items-center h-14">

        <div
            class="
                w-10
                sm:w-12
                h-full
                flex
                justify-center
                items-center
            "
        >
            <x-atoms.checkbox wire:model.live="selectAll" />
        </div>

        <div class="h-6 w-px bg-gray-300 mx-1 sm:mx-2"></div>

        <div class="flex items-center gap-1 ml-1">

            @if($onFilter)

                <button
                    wire:click="{{ $onFilter }}"
                    class="
                        w-9
                        h-9
                        flex
                        items-center
                        justify-center
                        p-2
                        text-gray-500
                        hover:text-brand-teal
                        hover:bg-gray-100
                        rounded-full
                        transition
                    "
                    title="Filter"
                >
                    <x-atoms.icon variant="filter" size="md"/>
                </button>

            @endif

            @if($onRefresh)

                <button
                    wire:click="{{ $onRefresh }}"
                    class="
                        w-9
                        h-9
                        flex
                        items-center
                        justify-center
                        p-2
                        text-gray-500
                        hover:text-brand-teal
                        hover:bg-gray-100
                        rounded-full
                        transition
                    "
                    title="Refresh"
                >
                    <x-atoms.icon variant="refresh" size="md"/>
                </button>

            @endif

        </div>

    </div>


    {{-- ACTIONS --}}
    @if(isset($actions) && $actions)

        <div
            class="
                flex
                items-center
                gap-2
                flex-wrap
                order-3
                sm:order-2
                w-full
                sm:w-auto
                sm:ml-auto
            "
        >
            {{ $actions }}
        </div>

    @endif


    {{-- PAGINATION --}}
    <div
        class="
            text-xs
            sm:text-sm
            text-gray-500
            order-2
            sm:order-3
            ml-auto
            sm:ml-0
            pr-0
            sm:pr-2
        "
    >
        {{ $pagination ?? '' }}
    </div>

</div>