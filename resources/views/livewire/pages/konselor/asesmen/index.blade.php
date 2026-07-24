<?php

use App\Livewire\Konselor\Asesmen\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {}; ?>

<div class="flex-1 bg-gray-50">

    <div class="px-8 py-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Halo, Konselor!
            </h1>

            <p class="text-gray-500 mt-2">
                Silakan pilih instrumen asesmen yang akan digunakan.
            </p>
        </div>

    <x-organisms.header>
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>

    </x-organisms.header>

        <div class="space-y-6">

            @foreach($asesmens as $item)

                <x-organisms.assessment-card
                    :title="$item['title']"
                    :subtitle="$item['subtitle']"
                    :description="$item['description']"
                    :details="$item['details']"
                    :route="$item['route']"
                    :button="$item['button']"
                    :variants="$item['variants']"
                    :color="$item['color']"
                />

            @endforeach

        </div>

    </div>

</div>