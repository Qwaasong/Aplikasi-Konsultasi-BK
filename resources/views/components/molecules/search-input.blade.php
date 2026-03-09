@props(['model'])

<div class="relative w-full max-w-xl">

    <x-atoms.text-input
        placeholder="Telusuri Nama Siswa"
        wire:model.live="{{ $model }}"
        size="lg"
    />

    <x-atoms.icon
        variant="search"
        size="lg"
        class="absolute right-3 top-3.5 text-gray-400"
    />

</div>