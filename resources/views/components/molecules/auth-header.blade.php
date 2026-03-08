@props([
    'title',
    'subtitle' => null,
    'linkText' => null,
    'linkHref' => '#',
])

<div {{ $attributes->merge(['class' => 'mb-8']) }}>
    <h1 class="text-3xl font-bold text-gray-900 mb-2">
        {{ $title }}
    </h1>
    
    @if($subtitle)
        <p class="text-sm text-gray-600">
            {{ $subtitle }}
            @if($linkText)
                <a href="{{ $linkHref }}" wire:navigate class="text-brand-teal font-medium hover:underline">
                    {{ $linkText }}
                </a>
            @endif
        </p>
    @endif
</div>