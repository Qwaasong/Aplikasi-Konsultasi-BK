@props(['bg' => 'bg-[#1E5C67] bg-[radial-gradient(circle_at_100%_0%,#7c2d12_0%,transparent_20%),radial-gradient(circle_at_30%_80%,rgba(255,255,255,0.1)_0%,transparent_50%)]'])

<div {{ $attributes->merge(['class' => "relative overflow-hidden rounded-[2rem] p-6 text-white $bg"]) }}>
    {{ $slot }}
</div>