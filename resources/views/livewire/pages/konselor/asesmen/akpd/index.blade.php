<?php

use App\Livewire\Konselor\Asesmen\Akpd\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {}; ?>

<div class="flex-1 flex flex-col bg-gray-50">

    <x-organisms.header>
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>
        AKPD
    </x-organisms.header>

    <div class="p-8">

        <x-organisms.assessment-card
            title="AKPD"
            subtitle="Angket Kebutuhan Peserta Didik"
            description="Halaman ini digunakan untuk mengisi Angket Kebutuhan Peserta Didik (AKPD)."
            :details="[
                'Mengenali kebutuhan peserta didik.',
                'Jawablah sesuai kondisi sebenarnya.',
                'Tidak ada jawaban benar maupun salah.'
            ]"
            :route="route('konselor.asesmen.akpd.index')"
            button="Mulai Mengisi AKPD"
            variants="assignment"
            color="teal"
        />

    </div>

</div>