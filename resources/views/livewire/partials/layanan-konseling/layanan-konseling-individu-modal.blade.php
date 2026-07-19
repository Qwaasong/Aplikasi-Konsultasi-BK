<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;
use App\Models\DataSiswa;
use App\Services\SiswaService;
use App\Services\BimbinganIndividuService;
use App\Models\TahunAjaran;

new class extends Component {

    public ?int $editingId = null;

    // ── DATA BIMBINGAN INDIVIDU ─────────────────────
    #[Validate('required|date')]
    public $tanggal_layanan = '';

    #[Validate('required|integer')]
    public $tahun_ajaran_id = '';

    #[Validate('required|string')]
    public $uraian_masalah = '';

    #[Validate('required|string')]
    public $penanganan = '';

    #[Validate('nullable|string')]
    public $tindak_lanjut = '';

    // ── SISWA ────────────────────────────────────────
    #[Validate('required|integer')]
    public $siswa_id = '';

    public $searchSiswa = '';
    public $showStudentModal = false;

    public function mount()
    {
        $this->tanggal_layanan = date('Y-m-d');
        $this->tahun_ajaran_id = TahunAjaran::where('status_aktif', true)->value('id')
            ?? TahunAjaran::latest()->value('id')
            ?? '';
    }

    public function selectStudent($id)
    {
        $this->siswa_id = (int) $id;
        $this->showStudentModal = false;
        $this->searchSiswa = '';
    }

    public function openStudentModal()
    {
        $this->showStudentModal = true;
    }

    public function closeStudentModal()
    {
        $this->showStudentModal = false;
    }

    #[Computed]
    public function selectedStudent()
    {
        if (!$this->siswa_id) return null;
        return app(SiswaService::class)->findById($this->siswa_id);
    }

    #[Computed]
    public function filteredStudents()
    {
        return app(SiswaService::class)->search($this->searchSiswa, 50);
    }

    public function getInitials($name)
    {
        if (!$name) return 'S';
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    // ── CREATE ───────────────────────────────────────
    #[On('create-bimbingan-individu')]
    public function createBimbinganIndividu()
    {
        $this->resetValidation();
        $this->reset([
            'editingId', 'tahun_ajaran_id', 'uraian_masalah',
            'penanganan', 'tindak_lanjut', 'siswa_id',
        ]);
        $this->tanggal_layanan = date('Y-m-d');
        $this->dispatch('open-modal', 'form-bimbingan-individu');
    }

    // ── EDIT ─────────────────────────────────────────
    #[On('edit-bimbingan-individu')]
    public function loadBimbinganIndividu($id)
    {
        $service = app(BimbinganIndividuService::class);
        $this->resetValidation();

        $record = $service->findById($id);

        $this->editingId = $id;
        $this->tahun_ajaran_id = $record->tahun_ajaran_id;
        $this->tanggal_layanan = \Carbon\Carbon::parse($record->tanggal_layanan)->format('Y-m-d');
        $this->uraian_masalah = $record->uraian_masalah;
        $this->penanganan = $record->penanganan;
        $this->tindak_lanjut = $record->tindak_lanjut;
        $this->siswa_id = $record->kasus?->siswa_id ?? '';

        $this->dispatch('open-modal', 'form-bimbingan-individu');
    }

    // ── SAVE ─────────────────────────────────────────
    public function save(BimbinganIndividuService $service)
    {
        $this->validate();

        $data = [
            'siswa_id' => $this->siswa_id,
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
            'tanggal_layanan' => $this->tanggal_layanan,
            'uraian_masalah' => $this->uraian_masalah,
            'penanganan' => $this->penanganan,
            'tindak_lanjut' => $this->tindak_lanjut,
        ];

        if ($this->editingId) {
            $service->update($this->editingId, $data);
            session()->flash('success', 'Layanan Konseling Individu berhasil diperbarui!');
        } else {
            $service->create($data);
            session()->flash('success', 'Layanan Konseling Individu berhasil ditambahkan!');
        }

        $this->reset([
            'editingId', 'tahun_ajaran_id', 'uraian_masalah',
            'penanganan', 'tindak_lanjut', 'siswa_id',
        ]);
        $this->tanggal_layanan = date('Y-m-d');

        $this->dispatch('close-modal', 'form-bimbingan-individu');
        $this->dispatch('refreshTable');
    }
}; ?>

<div>
    <x-shared.modal name="form-bimbingan-individu" maxWidth="lg">
    <div class="flex flex-col h-full max-h-[80vh]">

        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">
                {{ $editingId ? 'Edit Layanan Konseling Individu' : 'Tambah Layanan Konseling Individu' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $editingId ? 'Perbarui data layanan konseling individu' : 'Catat layanan konseling individu baru' }}
            </p>
        </div>

        <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">

            {{-- SISWA ────────────────────────────────────────--}}
            <div class="mb-6">
                <x-atoms.input-label for="id_siswa" size="sm">
                    Siswa <span class="text-red-500">*</span>
                </x-atoms.input-label>
                @if($this->selectedStudent)
                    <div class="bg-bg-light border border-teal-100/60 rounded-lg p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-[45px] h-[45px] bg-icon-bg text-primary rounded-full flex items-center justify-center font-bold text-[16px]">
                                {{ $this->getInitials($this->selectedStudent->nama_lengkap ?? $this->selectedStudent->nama) }}
                            </div>
                            <div>
                                <h3 class="text-[14px] font-bold text-gray-900">
                                    {{ $this->selectedStudent->nama_lengkap ?? $this->selectedStudent->nama }}</h3>
                                <p class="text-[12px] text-gray-400 mt-0.5">Kelas {{ $this->selectedStudent->kelas_label }}
                                    {{ $this->selectedStudent->jurusan_label }} - NIS {{ $this->selectedStudent->nis }}</p>
                            </div>
                        </div>
                        <button type="button" wire:click="openStudentModal" class="text-[13px] font-bold text-gray-500 hover:text-gray-800 transition-colors">
                            Ganti
                        </button>
                    </div>
                @else
                    <div class="bg-bg-light border border-teal-100/60 rounded-lg p-5 flex flex-col items-center justify-center text-center">
                        <div class="w-[56px] h-[56px] bg-icon-bg rounded-full flex items-center justify-center mb-3 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <h3 class="text-[15px] font-bold text-gray-700 mb-1">Tidak Ada Siswa Yang Dipilih</h3>
                        <p class="text-[13px] text-gray-400 mb-4">Pilih Siswa Untuk Melanjutkan</p>
                        <button type="button" wire:click="openStudentModal" class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-md text-[13px] font-semibold transition-colors">
                            Pilih Siswa
                        </button>
                    </div>
                    @error('siswa_id') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
                @endif
            </div>

            {{-- TANGGAL LAYANAN ──────────────────────────────--}}
            <div class="mb-6">
                <x-atoms.input-label for="tanggal_layanan" size="sm">
                    Tanggal Layanan <span class="text-red-500">*</span>
                </x-atoms.input-label>
                <x-atoms.text-input id="tanggal_layanan" type="date" wire:model="tanggal_layanan" size="md" />
                @error('tanggal_layanan') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
            </div>

            {{-- TAHUN AJARAN ──────────────────────────────────--}}
            <div class="mb-6">
                <x-atoms.input-label for="tahun_ajaran_id" size="sm">
                    Tahun Ajaran <span class="text-red-500">*</span>
                </x-atoms.input-label>
                <select id="tahun_ajaran_id" wire:model="tahun_ajaran_id"
                    class="w-full border border-gray-200 rounded-md px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                    <option value="">Pilih Tahun Ajaran</option>
                    @foreach(TahunAjaran::orderByDesc('tahun')->get() as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->tahun }} - {{ $ta->semester }} {{ $ta->status_aktif ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
                @error('tahun_ajaran_id') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
            </div>

            {{-- URAIAN MASALAH ────────────────────────────────--}}
            <div class="mb-6">
                <x-atoms.input-label for="uraian_masalah" size="sm">
                    Uraian Masalah <span class="text-red-500">*</span>
                </x-atoms.input-label>
                <textarea id="uraian_masalah" wire:model="uraian_masalah" rows="3"
                    class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                    placeholder="Tuliskan topik/uraian masalah yang dibahas..."></textarea>
                @error('uraian_masalah') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
            </div>

            {{-- PENANGANAN ────────────────────────────────────--}}
            <div class="mb-6">
                <x-atoms.input-label for="penanganan" size="sm">
                    Penanganan <span class="text-red-500">*</span>
                </x-atoms.input-label>
                <textarea id="penanganan" wire:model="penanganan" rows="3"
                    class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                    placeholder="Tuliskan penanganan/tujuan layanan..."></textarea>
                @error('penanganan') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
            </div>

            {{-- TINDAK LANJUT ─────────────────────────────────--}}
            <div class="mb-6">
                <x-atoms.input-label for="tindak_lanjut" size="sm">
                    Tindak Lanjut
                </x-atoms.input-label>
                <textarea id="tindak_lanjut" wire:model="tindak_lanjut" rows="2"
                    class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                    placeholder="Tuliskan hasil dan tindak lanjut (opsional)..."></textarea>
                @error('tindak_lanjut') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
            </div>

        </div>

        {{-- FOOTER ACTIONS ────────────────────────────────--}}
        <div class="bg-bg-light px-7 py-5 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl gap-3">
            <x-atoms.button variant="secondary" size="md" x-on:click="show = false">Batal</x-atoms.button>
            <x-atoms.button wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">
                    {{ $editingId ? 'Perbarui' : 'Simpan' }}
                </span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </x-atoms.button>
        </div>
    </div>

    {{-- MODAL PEMILIHAN SISWA ─────────────────────────--}}
    <div x-data="{ showStudentMenu: @entangle('showStudentModal') }" x-show="showStudentMenu"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        style="display: none;">
        <div class="bg-white w-full max-w-[500px] rounded-xl shadow-2xl flex flex-col max-h-[80vh] overflow-hidden"
            @click.away="showStudentMenu = false">
            <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0 flex justify-between items-center">
                <div>
                    <h2 class="text-[20px] font-bold text-gray-900 leading-tight">Pilih Siswa</h2>
                    <p class="text-[13px] text-gray-500 mt-0.5">Semua Siswa</p>
                </div>
            </div>

            <div class="px-6 py-5 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">
                <div class="flex gap-3 mb-5">
                    <div class="relative grow">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                        </div>
                        <input type="text" wire:model.live="searchSiswa" placeholder="Cari Nama Atau NIS" class="w-full border border-gray-200 rounded-md pl-10 pr-3 py-2 text-[13px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    @forelse($this->filteredStudents as $siswa)
                        <div wire:click="selectStudent({{ $siswa->id }})"
                            class="border border-gray-200 rounded-md p-4 cursor-pointer hover:border-primary hover:bg-bg-light transition-colors {{ $siswa_id == $siswa->id ? 'border-primary bg-bg-light' : '' }}">
                            <h4 class="text-[14px] font-bold text-gray-900">{{ $siswa->nama_lengkap ?? $siswa->nama }}</h4>
                            <p class="text-[12px] text-gray-500 mt-1">NIS: {{ $siswa->nis }} <span class="ml-2">Kelas: {{ $siswa->kelas_label }}</span></p>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 text-sm">Tidak ada siswa ditemukan.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-2.5">
                <button type="button" wire:click="closeStudentModal" class="px-5 py-2 bg-white border border-gray-200 rounded-md text-[13px] font-bold text-gray-600 hover:bg-gray-50 transition-colors shadow-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
    </x-shared.modal>
</div>
