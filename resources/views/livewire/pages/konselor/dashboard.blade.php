<?php

use App\Livewire\Konselor\Dashboard;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Dashboard {}; ?>

<div>
    <div class="py-8">
        <div class="mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Header --}}
            <x-molecules.header-card title="Selamat datang, {{ auth()->user()->nama }}!" badge="Dashboard Konselor"
                bgBadge="bg-white" textBadge="text-[#086375]" class="!text-white !bg-[#086375]">
                <div
                    class="bg-[#e0f7fa]/20 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg mt-4 border border-[#086375]/20">
                    <div class="p-6 font-medium text-white">
                        Anda masuk sebagai {{ auth()->user()->nama }}.
                    </div>
                </div>
            </x-molecules.header-card>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Card Kelas 10 --}}
                <x-molecules.stat-card label="Total Konsultasi Kelas 10" :value="$countKelas10"
                    textContainerClass="bg-white" bgClassIcon="bg-white" class="!bg-[#2F6B3F] border-none"
                    :showButton="false" textColor="text-[#2F6B3F]" iconBorderColor="border-none">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="#2F6B3F" />
                    </x-slot>
                </x-molecules.stat-card>

                {{-- Card Kelas 11 --}}
                <x-molecules.stat-card label="Total Konsultasi Kelas 11" :value="$countKelas11"
                    textContainerClass="bg-white" bgClassIcon="bg-white" class="!bg-[#C89B3C] border-none"
                    :showButton="false" textColor="text-[#C89B3C]" iconBorderColor="border-none">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="#C89B3C" />
                    </x-slot>
                </x-molecules.stat-card>

                {{-- Card Kelas 12 --}}
                <x-molecules.stat-card label="Total Konsultasi Kelas 12" :value="$countKelas12"
                    textContainerClass="bg-white" bgClassIcon="bg-white" class="!bg-[#A64632] border-none"
                    iconBorderColor="border-none" :showButton="false" textColor="text-[#A64632]">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="#A64632" />
                    </x-slot>
                </x-molecules.stat-card>

            </div>
        </div>
    </div>
</div>