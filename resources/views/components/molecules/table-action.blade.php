@props(['id'])

<div class="hidden group-hover:flex absolute inset-0 items-center justify-end pr-4 bg-transparent pl-4 gap-1"
    onclick="event.stopPropagation()">
    <x-atoms.action-button color="blue" title="Edit" wire:click="edit({{ $id }})">
        <x-atoms.icon variant="edit" size="sm" />
        {{ $edit ?? '' }}
    </x-atoms.action-button>

    <x-atoms.action-button color="red" title="Hapus" wire:click="delete({{ $id }})"
        wire:confirm="Yakin ingin menghapus data ini?">
        <x-atoms.icon variant="delete" size="sm" />
        {{ $delete ?? '' }}
    </x-atoms.action-button>
</div>