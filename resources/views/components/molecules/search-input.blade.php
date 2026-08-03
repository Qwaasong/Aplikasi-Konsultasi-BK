@props([
    'model',
    'maxWidth' => 'max-w-xl'
])

<div class="relative w-full {{ $maxWidth }}">

    <x-atoms.text-input
        placeholder="Telusuri Nama Siswa"
        wire:model.live="{{ $model }}"
        size="md"
    />

    <x-atoms.icon
        variant="search"
        size="md"
        class="absolute right-3 top-3.5 text-gray-400"
    />

</div>