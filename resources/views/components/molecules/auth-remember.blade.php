@props([
    'label' => 'Remember me',
    'id' => 'remember',
    'name' => 'remember',
    'type' => 'checkbox',
    'value' => 'remember',
    'checked' => false,
])
<div class="flex items-center">
    <x-atoms.checkbox :id="$id" :name="$name" :value="$value" :checked="$checked"
        {{ $attributes->whereStartsWith('wire:') }}
    />
    <x-atoms.input-label :for="$id" :id="$id" size="sm" class="ml-2 !mb-0">{{ $label }}</x-atoms.input-label>
</div>