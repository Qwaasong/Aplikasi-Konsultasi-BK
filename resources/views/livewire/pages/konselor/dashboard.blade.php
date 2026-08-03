<?php

use App\Livewire\Konselor\Dashboard;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app', ['title' => 'Dashboard - Bimbingan Konseling'])] class extends Dashboard {}; ?>


<div class="py-12">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <x-molecules.header-card title="Selamat datang {{ Auth::user()->nama }}!" badge="Dashboard Konselor"
            bgBadge="bg-white" textBadge="text-[#086375]" class="!bg-[#086375] !text-white">
            <div
                class="bg-[#e0f7fa]/20 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg mt-4 border border-[#086375]/20">
                <div class="p-6 font-medium">
                    Anda masuk sebagai {{ auth()->user()->nama }}.
                </div>
            </div>
        </x-molecules.header-card>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Kelas 10 --}}
            <x-molecules.stat-card label="Siswa Kelas 10" textColor='text-[#086375]' textContainerClass="bg-white"
                :value="$countKelas10" bgClassIcon="bg-white" color="blue">
                <x-slot name="icon">
                    <x-atoms.icon variant="student" size="lg" color="#086375" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Kelas 11 --}}
            <x-molecules.stat-card label="Siswa Kelas 11" textColor='text-[#086375]' textContainerClass="bg-white"
                bgClassIcon="bg-white" :value="$countKelas11" color="teal">
                <x-slot name="icon">
                    <x-atoms.icon variant="student" size="lg" color="#086375" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Kelas 12 --}}
            <x-molecules.stat-card label="Siswa Kelas 12" textColor='text-[#086375]' textContainerClass="bg-white"
                bgClassIcon="bg-white" :value="$countKelas12" color="kuning">
                <x-slot name="icon">
                    <x-atoms.icon variant="student" size="lg" color="#086375" />
                </x-slot>
            </x-molecules.stat-card>

        </div>
    </div>
</div>