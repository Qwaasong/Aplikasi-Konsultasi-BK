<?php

use App\Livewire\Admin\Dashboard;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Dashboard {}; ?>


<div class="py-12">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <x-molecules.header-card title="Selamat datang {{ Auth::user()->nama }}!" badge="Dashboard Admin"
            bgBadge="bg-white" textBadge="text-[#086375]" class="!bg-[#086375] !text-white">
            <div
                class="bg-[#e0f7fa]/20 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg mt-4 border border-[#086375]/20">
                <div class="p-6 font-medium">
                    Anda masuk sebagai {{ auth()->user()->nama }}.
                </div>
            </div>
        </x-molecules.header-card>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Total Pengguna --}}
            <x-molecules.stat-card label="Total Pengguna" textColor='text-[#086375]' textContainerClass="bg-white"
                :value="$totalUsers" bgClassIcon="bg-white" color="blue" url="{{ route('admin.user.index') }}">
                <x-slot name="icon">
                    <x-atoms.icon variant="user" size="lg" color="#086375" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Total Kasus BK --}}
            <x-molecules.stat-card label="Total Kasus BK" textColor='text-[#086375]' textContainerClass="bg-white"
                bgClassIcon="bg-white" :value="$totalKasus" color="teal"
                url="{{ route('admin.kasus-bk.index') }}">
                <x-slot name="icon">
                    <x-atoms.icon variant="consultation" size="lg" color="#086375" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Total Konselor --}}
            <x-molecules.stat-card label="Total Konselor" textColor='text-[#086375]' textContainerClass="bg-white"
                :value="$totalKonselor" bgClassIcon="bg-white" color="kuning" url="{{ route('admin.user.index') }}">
                <x-slot name="icon">
                    <x-atoms.icon variant="teacher" size="lg" color="#086375" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Total Siswa --}}
            <x-molecules.stat-card label="Total Siswa" textColor='text-[#086375]' textContainerClass="bg-white"
                :value="$totalSiswa" bgClassIcon="bg-white" color="red" url="{{ route('admin.siswa.index') }}">
                <x-slot name="icon">
                    <x-atoms.icon variant="student" size="lg" color="#086375" />
                </x-slot>
            </x-molecules.stat-card>

        </div>
    </div>
</div>