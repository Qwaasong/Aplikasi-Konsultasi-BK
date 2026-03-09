@props([
    'onFilter' => null,
    'onRefresh' => null
])

<div class="h-14 border-b border-gray-200 flex items-center justify-between bg-white shrink-0">

    <div class="flex items-center h-full">

        <div class="w-16 h-full flex justify-center items-center border-r-0 border-gray-100">
            <x-atoms.checkbox />
        </div>

        <div class="h-6 w-px bg-gray-300 mx-2"></div>

        <div class="flex items-center gap-1 ml-2">

            @if($onFilter)
                <button wire:click="{{ $onFilter }}" class="w-9 h-9 flex items-center justify-center p-2 text-gray-500 hover:text-brand-teal hover:bg-gray-100 rounded-full transition duration-200 tooltip" title="Filter">
                    <x-atoms.icon variant="filter" size="md"/>
                </button>
            @endif

            @if($onRefresh)
                <button wire:click="{{ $onRefresh }}" class="w-9 h-9 flex items-center justify-center p-2 text-gray-500 hover:text-brand-teal hover:bg-gray-100 rounded-full transition duration-200 tooltip" title="Refresh">
                    <x-atoms.icon variant="refresh" size="md"/>
                </button>
            @endif

        </div>

    </div>

    <div class="pr-6 text-sm text-gray-500">
        {{ $pagination ?? '' }}
    </div>
</div>