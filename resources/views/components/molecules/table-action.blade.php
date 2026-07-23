@props(['id'])

<div class="flex items-center justify-end gap-2">
    <x-atoms.action-button color="blue" title="Edit" wire:click="edit({{ $id }})">
        <x-atoms.icon variant="edit" size="sm" />
    </x-atoms.action-button>

    <x-atoms.action-button color="red" title="Hapus" wire:click="delete({{ $id }})"
        wire:confirm="Yakin ingin menghapus data ini?">
        <x-atoms.icon variant="delete" size="sm" />
    </x-atoms.action-button>
</div>