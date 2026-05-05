<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    //
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
                        {{ __('Anda masuk sebagai konselor.') }}
                    </div>
                </div>
            </x-molecules.header-card>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Card Kelas 10 --}}
                <x-molecules.stat-card label="Pengguna Kelas 10" value="10" color="teal" :showButton="false"
                    textColor="text-[#FCFFFD]"> {{-- Teks akan menjadi hitam --}}

                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="black" />
                    </x-slot>
                </x-molecules.stat-card>

                {{-- Card Kelas 11 --}}
                <x-molecules.stat-card label="Pengguna Kelas 11" value="5" color="kuning" :showButton="false" textColor="text-[#FCFFFD]">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="black" />
                    </x-slot>
                    <div class="mt-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        Kelas 11
                    </div>
                </x-molecules.stat-card>

                {{-- Card Kelas 12 --}}
                <x-molecules.stat-card label="Pengguna Kelas 12" value="20" color="red" :showButton="false" textColor="text-[#FCFFFD]">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="black" />
                    </x-slot>
                    <div class="mt-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        Kelas 12
                    </div>
                </x-molecules.stat-card>

            </div>
        </div>
    </div>
</div>
