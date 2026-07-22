<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {

};

?>

<div class="flex flex-col h-full">

    <x-organisms.header>
        <x-slot:search>
            <x-molecules.search-input model="search" />
        </x-slot:search>

        Form Sosiometri
    </x-organisms.header>

    <div class="flex-1 overflow-y-auto p-8">

        <div class="max-w-5xl mx-auto bg-white rounded-2xl border border-gray-200 shadow-sm p-8">

            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">
                    Form Sosiometri
                </h1>

                <p class="text-gray-600 mt-2">
                    Sosiometri digunakan untuk mengetahui hubungan sosial antar peserta didik dalam satu kelas.
                    Jawablah sesuai kondisi sebenarnya.
                </p>
            </div>

            <form class="space-y-6">

                {{-- Nama Siswa --}}
                <div>
                    <x-atoms.input-label for="nama">
                        Nama Siswa
                    </x-atoms.input-label>

                    <x-atoms.text-input
                        id="nama"
                        wire:model="nama"
                        placeholder="Masukkan nama siswa" />
                </div>

                {{-- Kelas --}}
                <div>
                    <x-atoms.input-label for="kelas">
                        Kelas
                    </x-atoms.input-label>

                    <x-atoms.text-input
                        id="kelas"
                        wire:model="kelas"
                        placeholder="Contoh : XI RPL 1" />
                </div>

                {{-- Pilihan Teman --}}
                <div>
                    <x-atoms.input-label for="pilihan1">
                        Pilihan Teman Pertama
                    </x-atoms.input-label>

                    <x-atoms.text-input
                        id="pilihan1"
                        wire:model="pilihan1"
                        placeholder="Nama teman" />
                </div>

                <div>
                    <x-atoms.input-label for="pilihan2">
                        Pilihan Teman Kedua
                    </x-atoms.input-label>

                    <x-atoms.text-input
                        id="pilihan2"
                        wire:model="pilihan2"
                        placeholder="Nama teman" />
                </div>

                <div>
                    <x-atoms.input-label for="pilihan3">
                        Pilihan Teman Ketiga
                    </x-atoms.input-label>

                    <x-atoms.text-input
                        id="pilihan3"
                        wire:model="pilihan3"
                        placeholder="Nama teman" />
                </div>

                {{-- Alasan --}}
                <div>
                    <x-atoms.input-label for="alasan">
                        Alasan Memilih
                    </x-atoms.input-label>

                    <textarea
                        id="alasan"
                        wire:model="alasan"
                        rows="4"
                        class="w-full border border-gray-200 rounded-lg p-4 focus:ring-primary focus:border-primary"
                        placeholder="Tuliskan alasan memilih teman tersebut..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">

                    <x-atoms.button
                        type="button"
                        variant="secondary">
                        Batal
                    </x-atoms.button>

                    <x-atoms.button
                        type="submit">
                        Simpan
                    </x-atoms.button>

                </div>

            </form>

        </div>

    </div>

</div>