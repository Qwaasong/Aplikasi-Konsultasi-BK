<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Services\UserService;
use App\Services\SiswaService;
use App\Services\KasusBkService;

new #[Layout('layouts.app')] class extends Component {
    public int $totalUsers = 0;
    public int $totalSiswa = 0;
    public int $totalKasus = 0;
    public int $totalKonselor = 0;

    public function mount(): void
    {
        $userService = app(UserService::class);
        $stats = $userService->getStats();

        $this->totalUsers = $stats['total'] ?? 0;
        $this->totalKonselor = $stats['konselor'] ?? 0;
        $this->totalSiswa = app(SiswaService::class)->getTotalSiswa();
        $this->totalKasus = app(KasusBkService::class)->countKasus();
    }
}; ?>

<div class="py-12">
    <div class="mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <x-molecules.header-card title="Selamat datang {{ Auth::user()->nama }}!" badge="Dashboard Admin"
            bgBadge="bg-white" textBadge="text-[#086375]" class="!bg-[#086375] !text-white">
            <div
                class="bg-[#e0f7fa]/20 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg mt-4 border border-[#086375]/20">
                <div class="p-6 font-medium">
                    {{ __("Anda masuk sebagai Admin.") }}
                </div>
            </div>
        </x-molecules.header-card>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Total Pengguna --}}
            <x-molecules.stat-card label="Total Pengguna" textColor='text-white' textContainerClass="bg-[#086375]"
                :value="$totalUsers" bgClassIcon="bg-[#086375]" color="emerald" url="{{ route('admin.user.index') }}">
                <x-slot name="icon">
                    <x-atoms.icon variant="user" size="lg" color="white" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Total Kasus BK --}}
            <x-molecules.stat-card label="Total Kasus BK" textColor='text-white' textContainerClass="bg-[#086375]"
                bgClassIcon="bg-[#086375]" :value="$totalKasus" color="ruby"
                url="{{ route('admin.kasus-bk.index') }}">
                <x-slot name="icon">
                    <x-atoms.icon variant="consultation" size="lg" color="white" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Total Konselor --}}
            <x-molecules.stat-card label="Total Konselor" textColor='text-white' textContainerClass="bg-[#086375]"
                :value="$totalKonselor" bgClassIcon="bg-[#086375]" color="purple" url="{{ route('admin.user.index') }}">
                <x-slot name="icon">
                    <x-atoms.icon variant="teacher" size="lg" color="white" />
                </x-slot>
            </x-molecules.stat-card>

            {{-- Total Siswa --}}
            <x-molecules.stat-card label="Total Siswa" textColor='text-white' textContainerClass="bg-[#086375]"
                :value="$totalSiswa" bgClassIcon="bg-[#086375]" color="yellow" url="{{ route('admin.siswa.index') }}">
                <x-slot name="icon">
                    <x-atoms.icon variant="student" size="lg" color="white" />
                </x-slot>
            </x-molecules.stat-card>

        </div>
    </div>
</div>