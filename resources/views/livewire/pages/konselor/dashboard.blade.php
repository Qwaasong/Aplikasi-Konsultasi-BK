<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Konsultasi;

new #[Layout('layouts.app')] class extends Component {
    public int $countKelas10 = 0;
    public int $countKelas11 = 0;
    public int $countKelas12 = 0;

    public function mount(): void
    {
        $this->countKelas10 = Konsultasi::whereHas('siswa', fn($q) => $q->where('kelas', 10))
            ->distinct()
            ->count('id_siswa');

        $this->countKelas11 = Konsultasi::whereHas('siswa', fn($q) => $q->where('kelas', 11))
            ->distinct()
            ->count('id_siswa');

        $this->countKelas12 = Konsultasi::whereHas('siswa', fn($q) => $q->where('kelas', 12))
            ->distinct()
            ->count('id_siswa');
    }
}; 
?>

<div>
    <div class="py-8">
        <div class="mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Header --}}
            <x-molecules.header-card title="Selamat datang, {{ auth()->user()->nama }}!" badge="Dashboard Konselor"
                bgBadge="bg-white" textBadge="text-[#086375]" class="!text-white !bg-[#086375]">
                <div
                    class="bg-[#e0f7fa]/20 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg mt-4 border border-[#086375]/20">
                    <div class="p-6 font-medium text-white">
                        Anda masuk sebagai {{ auth()->user()->role }}.
                    </div>
                </div>
            </x-molecules.header-card>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Card Kelas 10 --}}
                <x-molecules.stat-card label="Total Konsultasi Kelas 10" :value="$countKelas10" textContainerClass="bg-white"
                    bgClassIcon="bg-white" class="!bg-[#2F6B3F] border-none" :showButton="false" textColor="text-[#086375]"
                    iconBorderColor="border-none">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="#086375" />
                    </x-slot>
                </x-molecules.stat-card>

                {{-- Card Kelas 11 --}}
                <x-molecules.stat-card label="Total Konsultasi Kelas 11" :value="$countKelas11" textContainerClass="bg-white" bgClassIcon="bg-white" class="!bg-[#C89B3C] border-none"
                    :showButton="false" textColor="text-[#086375]" iconBorderColor="border-none">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="#086375" />
                    </x-slot>
                </x-molecules.stat-card>

                {{-- Card Kelas 12 --}}
                <x-molecules.stat-card label="Total Konsultasi Kelas 12" :value="$countKelas12" textContainerClass="bg-white" bgClassIcon="bg-white" class="!bg-[#A64632] border-none"
                    iconBorderColor="border-none" :showButton="false" textColor="text-[#086375]">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="#086375" />
                    </x-slot>
                </x-molecules.stat-card>

            </div>
        </div>
    </div>
</div>