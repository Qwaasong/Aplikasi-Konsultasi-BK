<?php

use App\Livewire\Konselor\Dashboard;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app', ['title' => 'Dashboard - Bimbingan Konseling'])] class extends Dashboard {
}; ?>

<div class="py-6 sm:py-8 lg:py-12">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">
        <x-molecules.header-card title="Selamat datang {{ Auth::user()->nama }}!" badge="Dashboard Konselor"
            bgBadge="bg-white" textBadge="text-[#086375]" class="!bg-[#086375] !text-white">
            <div
                class="bg-[#e0f7fa]/20 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg mt-4 border border-[#086375]/20">
                <div class="p-4 sm:p-6 font-medium text-sm sm:text-base">
                    Anda masuk sebagai <span class="font-bold">{{ auth()->user()->role == 'guru_bk' ? 'Konselor' : ucfirst(auth()->user()->role) }}</span>.
                </div>
            </div>
        </x-molecules.header-card>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">

            {{-- Kelas 10 --}}
            <x-molecules.stat-card label="Siswa Kelas 10" textColor='text-[#086375]' textContainerClass="bg-white"
                :value="$countKelas10" bgClassIcon="bg-white" color="blue" :show-button="false">
                <x-slot name="icon">
                    <x-atoms.icon variant="student" size="lg" color="#086375" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Kelas 11 --}}
            <x-molecules.stat-card label="Siswa Kelas 11" textColor='text-[#086375]' textContainerClass="bg-white"
                bgClassIcon="bg-white" :value="$countKelas11" color="teal" :show-button="false">
                <x-slot name="icon">
                    <x-atoms.icon variant="student" size="lg" color="#086375" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Kelas 12 --}}
            <x-molecules.stat-card label="Siswa Kelas 12" textColor='text-[#086375]' textContainerClass="bg-white"
                bgClassIcon="bg-white" :value="$countKelas12" color="kuning" :show-button="false">
                <x-slot name="icon">
                    <x-atoms.icon variant="student" size="lg" color="#086375" />
                </x-slot>
            </x-molecules.stat-card>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-stretch">

            {{-- Gaya Belajar --}}
            <div class="lg:col-span-2 bg-white border border-[#086375]/15 rounded-2xl p-4 sm:p-6 shadow-sm h-full flex flex-col">

                <h2 class="text-lg font-semibold text-[#086375] mb-4">
                    Gaya Belajar Siswa
                </h2>

                <div class="flex-1">
                    <x-molecules.radial-chart :data="$gayaBelajarData" />
                </div>

                <div class="mt-6">
                    <x-atoms.detail-link
                        text="Lihat Detail"
                        href="asesmen/gaya-belajar"/>
                </div>

            </div>


            {{-- Bakat & Minat --}}
            <div class="lg:col-span-3 bg-white border border-[#086375]/15 rounded-2xl p-4 sm:p-6 shadow-sm h-full flex flex-col">

                <h2 class="text-lg font-semibold text-[#086375] mb-4">
                    Bakat & Minat Siswa
                </h2>

                <div class="flex-1">
                    <x-molecules.bar-chart :data="$bakatMinatData" />
                </div>

                <div class="mt-6">
                    <x-atoms.detail-link
                        text="Lihat Detail"
                        href="{{ route('konselor.asesmen.tes-bakat-minat.index') }}" />
                </div>

            </div>

            {{-- Hasil AKPD --}}
            <div class="lg:col-span-5 bg-white border border-[#086375]/15 rounded-2xl p-6 shadow-sm">

                <h2 class="text-lg font-semibold text-[#086375] mb-4">
                    Hasil AKPD
                </h2>

                <x-molecules.akpd-chart :data="$akpdData" />

                <div class="mt-6">
                    <x-atoms.detail-link
                        text="Lihat Detail"
                        href="asesmen/akpd" />
                </div>

            </div>

        </div>
    </div>
</div>