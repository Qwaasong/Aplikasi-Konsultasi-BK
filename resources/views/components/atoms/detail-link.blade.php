@props([
    'text' => 'Lihat Detail',
    'href' => '#',
])

<a
    href="{{ $href }}"
    {{ $attributes }}
    class="inline-flex items-center gap-2 text-sm font-medium text-gray-400
           hover:text-[#086375] transition-colors duration-200"
>
    <span>{{ $text }}</span>

    <x-atoms.icon
        variant="chevron_right"
        size="sm"
        color="#9CA3AF"
    />
</a>