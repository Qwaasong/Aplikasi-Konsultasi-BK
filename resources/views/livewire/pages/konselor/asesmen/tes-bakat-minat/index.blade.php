<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {

};

?>

<div class="flex-1 flex flex-col bg-gray-50">

    <x-organisms.header>
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>
        Tes Bakat Minat
    </x-organisms.header>

    <div class="p-8">

        <x-organisms.assessment-card
            title="Tes Bakat Minat"
            subtitle="Tes Potensi Bakat dan Minat"
            description="Halaman ini digunakan untuk mengetahui kecenderungan bakat dan minat peserta didik."
            :details="[
                'Jawablah sesuai kondisi diri.',
                'Tidak ada jawaban benar atau salah.',
                'Hasil digunakan sebagai bahan layanan BK.'
            ]"
            :route="route('konselor.asesmen.tes-bakat-minat.index')"
            button="Mulai Tes Bakat Minat"
            variants="analytics"
            color="teal"
        />

    </div>

</div>