<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    //
}; ?>

{{--<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Admin Dashboard') }}
    </h2>
</x-slot> --}}

<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <x-molecules.header-card title="Selamat datang di Panel Admin!" badge="Dashboard Admin">
            <div
                class="bg-white/10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg mt-4 border border-white/20">
                <div class="p-6 text-white font-medium">
                    {{ __("Anda masuk sebagai Administrator. Kelola konsultasi BK dengan bijak.") }}
                </div>
            </div>
        </x-molecules.header-card>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">

            {{-- Total Pengguna --}}
            <x-molecules.stat-card label="Total Pengguna" value="10" color="emerald">
                <x-slot name="icon">
                    <x-atoms.icon variant="user" size="lg" color="white" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Total Konsultasi --}}
            <x-molecules.stat-card label="Total Konsultasi" value="5" color="ruby">
                <x-slot name="icon">
                    <x-atoms.icon variant="consultation" size="lg" color="white" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Total Guru --}}
            <x-molecules.stat-card label="Total Guru" value="20" color="purple">
                <x-slot name="icon">
                    <x-atoms.icon variant="teacher" size="lg" color="white" />
                </x-slot>
            </x-molecules.stat-card>

            
            {{-- Isian card masih diperhitungkan card hanyalah contoh jika pun masih ada kelebihan bisa dirauh di card bawah --}}
            {{-- Total Konsultasi --}}
            <x-molecules.stat-card label="Total Konsultasi" value="5" color="yellow">
                <x-slot name="icon">
                    <x-atoms.icon variant="consultation" size="lg" color="white" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Total Konsultasi --}}
            <x-molecules.stat-card label="Total Konsultasi" value="5" color="green">
                <x-slot name="icon">
                    <x-atoms.icon variant="consultation" size="lg" color="white" />
                </x-slot>
            </x-molecules.stat-card>

        </div>
    </div>
</div>