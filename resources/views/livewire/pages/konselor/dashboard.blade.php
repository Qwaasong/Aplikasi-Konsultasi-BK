<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\DataSiswa;

new #[Layout('layouts.app')] class extends Component {
    public int $countKelas10 = 0;
    public int $countKelas11 = 0;
    public int $countKelas12 = 0;

    public function mount(): void
    {
        $this->countKelas10 = DataSiswa::byKelas(10)->count();
        $this->countKelas11 = DataSiswa::byKelas(11)->count();
        $this->countKelas12 = DataSiswa::byKelas(12)->count();
    }
}; ?>

{{-- <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Counselor Dashboard') }}
</h2>
</x-slot> --}}

<div>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Header --}}
            <x-molecules.header-card title="Selamat datang, {{ auth()->user()->name ?? 'Konselor' }}!"
                badge="Dashboard Konselor" class="bg-yellow !text-[#086375]">
                <div
                    class="bg-[#e0f7fa]/20 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg mt-4 border border-[#086375]/20">
                    <div class="p-6 font-medium text-[#086375]">
                        Anda masuk sebagai {{ auth()->user()->nama ?? 'Konselor' }}.
                    </div>
                </div>
            </x-molecules.header-card>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Card Kelas 10 --}}
                <x-molecules.stat-card label="Pengguna Kelas 10" :value="$countKelas10" color="teal" :showButton="false"
                    textColor="text-[#FCFFFD]">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="black" />
                    </x-slot>
                </x-molecules.stat-card>

                {{-- Card Kelas 11 --}}
                <x-molecules.stat-card label="Pengguna Kelas 11" :value="$countKelas11" color="kuning" :showButton="false"
                    textColor="text-[#FCFFFD]">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="black" />
                    </x-slot>
                </x-molecules.stat-card>

                {{-- Card Kelas 12 --}}
                <x-molecules.stat-card label="Pengguna Kelas 12" :value="$countKelas12" color="red" :showButton="false"
                    textColor="text-[#FCFFFD]">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="black" />
                    </x-slot>
                </x-molecules.stat-card>

            </div>
        </div>
    </div>
</div>

