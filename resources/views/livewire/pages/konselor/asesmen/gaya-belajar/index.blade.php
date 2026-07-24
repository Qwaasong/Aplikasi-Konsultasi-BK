<?php

use App\Livewire\Konselor\Asesmen\GayaBelajar\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {}; ?>

<div class="flex-1 flex flex-col bg-gray-50">

    <x-organisms.header>
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>
        Gaya Belajar
    </x-organisms.header>

    <div class="p-8">

        <x-organisms.assessment-card
            title="Gaya Belajar"
            subtitle="Tes Gaya Belajar"
            description="Halaman ini digunakan untuk mengetahui kecenderungan gaya belajar peserta didik."
            :details="[
                'Visual.',
                'Auditori.',
                'Kinestetik.'
            ]"
            :route="route('konselor.asesmen.gaya-belajar.index')"
            button="Mulai Tes Gaya Belajar"
            variants="book"
            color="teal"
        />

    </div>

</div>