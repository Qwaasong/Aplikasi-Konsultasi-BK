@props([
    'model',
    'maxWidth' => 'max-w-xl'
])

<div
    class="
        relative
        w-full
        {{ $maxWidth }}
        sm:max-w-xl
    "
>

    <x-atoms.text-input
        placeholder="Telusuri Nama Siswa"
        wire:model.live="{{ $model }}"
        size="md"
        class="pr-10"
    />

    <x-atoms.icon
        variant="search"
        size="md"
        class="
            absolute
            right-3
            top-1/2
            -translate-y-1/2
            text-gray-400
        "
    />

</div>