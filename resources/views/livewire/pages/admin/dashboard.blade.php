<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\UserService;
use App\Services\SiswaService;
use App\Services\KonsultasiService;

new #[Layout('layouts.app')] class extends Component {
    public int $totalUsers = 0;
    public int $totalSiswa = 0;
    public int $totalKonsultasi = 0;
    public int $totalKonselor = 0;

    public function mount(): void
    {
        $userService = app(UserService::class);
        $stats = $userService->getStats();

        $this->totalUsers = $stats['total'] ?? 0;
        $this->totalKonselor = $stats['konselor'] ?? 0;
        $this->totalSiswa = app(SiswaService::class)->getTotalSiswa();
        $this->totalKonsultasi = app(KonsultasiService::class)->getTotalKonsultasi();
    }
}; ?>

{{--<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Admin Dashboard') }}
    </h2>
</x-slot> --}}

<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <x-molecules.header-card title="Selamat datang di Panel Admin!" badge="Dashboard Admin" class="bg-white !text-[#086375]">
            <div
                class="bg-[#e0f7fa]/20 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg mt-4 border border-[#086375]/20">
                <div class="p-6 font-medium">
                    {{ __("Anda masuk sebagai Administrator. Kelola konsultasi BK dengan bijak.") }}
                </div>
            </div>
        </x-molecules.header-card>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">

            {{-- Total Pengguna --}}
            <x-molecules.stat-card label="Total Pengguna" :value="$totalUsers" color="emerald" url="{{ route('admin.user.index') }}">
                <x-slot name="icon">
                    <x-atoms.icon variant="user" size="lg" color="white" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Total Konsultasi --}}
            <x-molecules.stat-card label="Total Konsultasi" :value="$totalKonsultasi" color="ruby" url="{{ route('admin.konsultasi.index') }}">
                <x-slot name="icon">
                    <x-atoms.icon variant="consultation" size="lg" color="white" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Total Konselor --}}
            <x-molecules.stat-card label="Total Konselor" :value="$totalKonselor" color="purple" url="{{ route('admin.user.index') }}">
                <x-slot name="icon">
                    <x-atoms.icon variant="teacher" size="lg" color="white" />
                </x-slot>
            </x-molecules.stat-card>


            {{-- Isian card masih diperhitungkan card hanyalah contoh jika pun masih ada kelebihan bisa dirauh di card bawah --}}
            {{-- Total Siswa --}}
            <x-molecules.stat-card label="Total Siswa" :value="$totalSiswa" color="yellow" url="{{ route('admin.siswa.index') }}">
                <x-slot name="icon">
                    <x-atoms.icon variant="student" size="lg" color="white" />
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
