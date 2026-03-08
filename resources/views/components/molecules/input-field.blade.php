@props([
    'label' => null,
    'id' => null,
    'type' => 'text',
    'name' => null,
    'placeholder' => null,
    'size' => 'md',
    'error' => null
])

@php
    $uniqueId = $id ?? $name ?? 'input-' . Str::random(8);
@endphp

<div class="mb-4">
    @if($label)
        <x-atoms.input-label :for="$uniqueId" size="md">
            {{ $label }}
        </x-atoms.input-label>
    @endif

    <x-atoms.text-input
        :id="$uniqueId"
        :type="$type"
        :name="$name ?? $uniqueId"
        :size="$size"
        :placeholder="$placeholder"
    />

    @if($error)
        <p class="mt-1 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>