<?php

use App\Livewire\Konselor\Asesmen\Dcm\Index;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Index {}; ?>

<div class="flex-1 flex flex-col bg-gray-50">

    <x-organisms.header>
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>
        DCM
    </x-organisms.header>

    <div class="p-8">

        <x-organisms.assessment-card
            title="DCM"
            subtitle="Daftar Cek Masalah"
            description="Halaman ini digunakan untuk mengidentifikasi masalah yang dialami peserta didik."
            :details="[
                'Jawablah dengan jujur.',
                'Data bersifat rahasia.',
                'Digunakan sebagai dasar layanan BK.'
            ]"
            :route="route('konselor.asesmen.dcm.index')"
            button="Mulai Mengisi DCM"
            variants="fact_check"
            color="teal"
        />

    </div>

</div>