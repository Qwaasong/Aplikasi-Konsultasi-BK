@props([
    'active' => false
])

<span {{ $attributes->class([
    'hide-text font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-300',
    'text-brand-teal' => $active,
    'group-hover/menu:text-brand-teal' => !$active,
]) }}>
    {{ $slot }}
</span>

<style>
    .hide-text {
        opacity: 1;
        width: auto;
    }
</style>
