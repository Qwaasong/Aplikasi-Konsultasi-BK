@props([
    'linkHref' => '#',
    'active' => false,
    'variants' => 'dashboard'
])

<a href="{{ $linkHref }}" wire:navigate 
    @class([
        'group/menu flex items-center h-12 w-full px-4',
        'text-gray-600' => !$active
    ])>
    
    <div @class([
        'flex items-center rounded-lg transition-all duration-300 w-12 group-hover:w-full h-12 overflow-hidden group-hover/menu:pr-4',
        'bg-brand-teal-light' => $active,
        'group-hover/menu:bg-gray-100' => !$active
    ])>
    <div @class([
        'w-12 h-12 flex-shrink-0 flex justify-center items-center',
        'text-brand-teal' => $active,
        'group-hover/menu:text-brand-teal transition-colors' => !$active
    ])>
        <x-atoms.icon :variant="$variants" size="lg"/>
    </div>
        <x-atoms.nav-label :active="$active">{{ $slot }}</x-atoms.nav-label>
    </div>
</a>
