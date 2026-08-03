<header class="h-20 border-b border-gray-200 px-8 flex items-center justify-between shrink-0">

    {{ $search }}

    @if(!empty($action))
        <x-atoms.button wire:click="{{ $action }}" variant="primary" size="md" class="flex items-center font-medium gap-2">
            <x-atoms.icon variant="plus" size="md" />
            {{ $slot }}
        </x-atoms.button>
    @else
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $slot }}
        </h2>
    @endif

</header>