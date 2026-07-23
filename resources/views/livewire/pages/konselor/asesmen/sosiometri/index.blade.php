<?php

use App\Livewire\Konselor\Asesmen\Sosiometri\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {}; ?>

<div class="flex-1 flex flex-col bg-gray-50">

    <x-organisms.header>
        <x-slot:search>
        <x-molecules.search-input model="search" />
    </x-slot:search>
        Sosiometri
    </x-organisms.header>

    <div class="p-8">


        <x-organisms.assessment-card
            title="Sosiometri"
            subtitle="Hubungan Sosial Peserta Didik"
            description="Halaman ini digunakan untuk mengetahui hubungan sosial antar peserta didik."
            :details="[
                'Jawablah dengan jujur.',
                'Hasil digunakan untuk layanan BK.',
                'Data bersifat rahasia.'
            ]"
            :route="route('konselor.asesmen.sosiometri.form')"
            button="Mulai Sosiometri"
            variants="group"
            color="teal"
        />

    </div>

</div>