<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    //
}; ?>

<div> <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white border border-brand-teal rounded-[1.5rem] p-8 relative overflow-hidden shadow-sm">
                <div class="relative z-10">
                    <span class="bg-[#086375] text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">
                        Dashboard Konselor
                    </span>
                    <h1 class="text-3xl font-bold text-brand-teal mt-5">
                        Selamat datang di Panel Konselor!
                    </h1>

                    <div class="mt-5 bg-teal-50/50 border border-teal-100 p-5 rounded-xl max-w-3xl">
                        <p class="text-brand-teal font-medium">
                            Anda masuk sebagai Konselor. Kelola bimbingan dan pantau perkembangan siswa dengan bijak.
                        </p>
                    </div>
                </div>

                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-teal-50 rounded-full opacity-50"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">

                {{-- Card Kelas 10 --}}
                <x-molecules.stat-card
                    label="TOTAL PENGGUNA"
                    value="10"
                    color="teal"
                    url="#">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="white" />
                    </x-slot>
                    <div class="mt-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        Kelas 10
                    </div>
                </x-molecules.stat-card>

                {{-- Card Kelas 11 --}}
                <x-molecules.stat-card
                    label="TOTAL PENGGUNA"
                    value="5"
                    color="teal"
                    url="#">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="white" />
                    </x-slot>
                    <div class="mt-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        Kelas 11
                    </div>
                </x-molecules.stat-card>

                {{-- Card Kelas 12 --}}
                <x-molecules.stat-card
                    label="TOTAL PENGGUNA"
                    value="20"
                    color="teal"
                    url="#">
                    <x-slot name="icon">
                        <x-atoms.icon variant="user" size="lg" color="white" />
                    </x-slot>
                    <div class="mt-1 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        Kelas 12
                    </div>
                </x-molecules.stat-card>

            </div>
        </div>
    </div>
</div>
