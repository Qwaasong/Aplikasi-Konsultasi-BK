<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Services\AlihTanganKasusService;

new class extends Component {

    public ?int $editingId = null;

    // Data alih tangan
    public $kasus_id = '';
    public $tanggal_alih = '';
    public $nama_penerima = '';
    public $alasan_alih = '';
    public $tindak_lanjut = '';

    // Pencarian kasus
    public $searchKasus = '';
    public $showKasusModal = false;

    public function mount()
    {
        $this->tanggal_alih = date('Y-m-d');
    }

    public function selectKasus($id)
    {
        $this->kasus_id = $id;
        $this->showKasusModal = false;
        $this->searchKasus = '';
    }

    public function openKasusModal()
    {
        $this->showKasusModal = true;
    }

    public function closeKasusModal()
    {
        $this->showKasusModal = false;
    }

    #[Computed]
    public function selectedKasus()
    {
        if (!$this->kasus_id) return null;
        return app(AlihTanganKasusService::class)->getKasusOptions()
            ->firstWhere('id', (int) $this->kasus_id);
    }

    #[Computed]
    public function kasusOptions()
    {
        $all = app(AlihTanganKasusService::class)->getKasusOptions();

        if ($this->searchKasus) {
            $needle = strtolower($this->searchKasus);
            $all = $all->filter(function ($k) use ($needle) {
                return str_contains(strtolower($k['nama_siswa'] ?? ''), $needle)
                    || str_contains(strtolower($k['penanganan'] ?? ''), $needle)
                    || str_contains($k['nis'] ?? '', $needle);
            })->values();
        }

        return $all;
    }

    #[Computed]
    public function guruBkOptions()
    {
        return app(AlihTanganKasusService::class)->getGuruBkOptions();
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

    // CREATE
    #[On('create-alih-tangan-kasus')]
    public function createAlihTanganKasus()
    {
        $this->resetValidation();
        $this->reset([
            'editingId', 'kasus_id', 'tanggal_alih', 'nama_penerima',
            'alasan_alih', 'tindak_lanjut', 'searchKasus', 'showKasusModal',
        ]);
        $this->tanggal_alih = date('Y-m-d');
        $this->dispatch('open-modal', 'form-alih-tangan-kasus');
    }

    // EDIT
    #[On('edit-alih-tangan-kasus')]
    public function loadAlihTanganKasus($id)
    {
        $service = app(AlihTanganKasusService::class);
        $this->resetValidation();
        $this->reset(['searchKasus', 'showKasusModal']);

        $record = $service->findById((int) $id);

        $this->editingId = $record->id;
        $this->kasus_id = $record->kasus_id;
        $this->tanggal_alih = $record->tanggal_alih
            ? \Carbon\Carbon::parse($record->tanggal_alih)->format('Y-m-d')
            : date('Y-m-d');
        $this->nama_penerima = $record->nama_penerima;
        $this->alasan_alih = $record->alasan_alih;
        $this->tindak_lanjut = $record->tindak_lanjut;

        $this->dispatch('open-modal', 'form-alih-tangan-kasus');
    }

    // SAVE
    public function save(AlihTanganKasusService $service)
    {
        $rules = [
            'kasus_id' => 'required|integer',
            'tanggal_alih' => 'required|date',
            'nama_penerima' => 'required|integer',
            'alasan_alih' => 'nullable|string',
            'tindak_lanjut' => 'nullable|string',
        ];

        $this->validate($rules);

        $data = [
            'kasus_id' => $this->kasus_id,
            'tanggal_alih' => $this->tanggal_alih,
            'nama_penerima' => $this->nama_penerima,
            'alasan_alih' => $this->alasan_alih,
            'tindak_lanjut' => $this->tindak_lanjut,
        ];

        if ($this->editingId) {
            $service->update($this->editingId, $data);
            session()->flash('success', 'Alih Tangan Kasus berhasil diperbarui!');
        } else {
            $service->create($data);
            session()->flash('success', 'Alih Tangan Kasus berhasil ditambahkan!');
        }

        $this->reset([
            'editingId', 'kasus_id', 'tanggal_alih', 'nama_penerima',
            'alasan_alih', 'tindak_lanjut', 'searchKasus', 'showKasusModal',
        ]);
        $this->tanggal_alih = date('Y-m-d');

        $this->dispatch('close-modal', 'form-alih-tangan-kasus');
        $this->dispatch('refreshTable');
    }
}; ?>

<div>
    <x-shared.modal name="form-alih-tangan-kasus" maxWidth="lg">
    <div class="flex flex-col h-full max-h-[80vh]">

        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">
                {{ $editingId ? 'Edit Alih Tangan Kasus' : 'Tambah Alih Tangan Kasus' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                Pilih kasus yang akan dialihkan ke guru BK lain
            </p>
        </div>

        <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">

            {{-- PILIH KASUS --}}
            <div class="mb-6">
                <x-atoms.input-label for="kasus_id" size="sm">
                    Kasus yang Dialihkan <span class="text-red-500">*</span>
                </x-atoms.input-label>
                @if($this->selectedKasus)
                    @php $k = $this->selectedKasus; @endphp
                    <div class="bg-bg-light border border-teal-100/60 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-[14px] font-bold text-gray-900">{{ $k['nama_siswa'] }}</h3>
                                <p class="text-[12px] text-gray-500 mt-0.5">
                                    {{ $k['penanganan'] }} &middot; {{ $k['kategori'] }}
                                </p>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    NIS {{ $k['nis'] }} &middot; Kelas {{ $k['kelas_label'] }}
                                    &middot; {{ $k['tanggal_mulai'] }}
                                </p>
                            </div>
                            <button type="button" wire:click="openKasusModal"
                                class="text-[13px] font-bold text-gray-500 hover:text-gray-800 transition-colors shrink-0 ml-2">
                                Ganti
                            </button>
                        </div>
                    </div>
                @else
                    <button type="button" wire:click="openKasusModal"
                        class="w-full border-2 border-dashed border-gray-300 rounded-lg p-5 text-sm text-gray-500 hover:border-teal-400 hover:text-teal-600 transition-colors text-center">
                        + Pilih Kasus
                    </button>
                @endif
                @error('kasus_id') <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span> @enderror
            </div>

            {{-- Tanggal Alih Tangan --}}
            <div class="mb-6">
                <x-atoms.input-label for="tanggal_alih" size="sm">
                    Tanggal Alih Tangan <span class="text-red-500">*</span>
                </x-atoms.input-label>
                <x-atoms.text-input id="tanggal_alih" type="date" wire:model="tanggal_alih" size="md" />
                @error('tanggal_alih')
                    <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Guru BK Penerima --}}
            <div class="mb-6">
                <x-atoms.input-label for="nama_penerima" size="sm">
                    Guru BK Penerima <span class="text-red-500">*</span>
                </x-atoms.input-label>
                <select id="nama_penerima" wire:model="nama_penerima"
                    class="w-full border border-gray-200 rounded-md px-4 py-3 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">
                    <option value="">Pilih Guru BK Penerima</option>
                    @foreach($this->guruBkOptions as $guru)
                        <option value="{{ $guru['id'] }}">
                            {{ $guru['nama'] }} ({{ $guru['nip'] }})
                        </option>
                    @endforeach
                </select>
                @error('nama_penerima')
                    <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Alasan Alih Tangan --}}
            <div class="mb-6">
                <x-atoms.input-label for="alasan_alih" size="sm">
                    Alasan Alih Tangan
                </x-atoms.input-label>
                <textarea id="alasan_alih" wire:model="alasan_alih" rows="3"
                    class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                    placeholder="Contoh: Membutuhkan penanganan psikolog karena memerlukan asesmen lanjutan"></textarea>
                @error('alasan_alih')
                    <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                @enderror
            </div>

            {{-- Tindak Lanjut --}}
            <div class="mb-6">
                <x-atoms.input-label for="tindak_lanjut" size="sm">
                    Tindak Lanjut
                </x-atoms.input-label>
                <textarea id="tindak_lanjut" wire:model="tindak_lanjut" rows="3"
                    class="w-full border border-gray-200 rounded-md p-4 text-[14px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none shadow-sm"
                    placeholder="Contoh: Guru BK melakukan monitoring perkembangan setelah proses alih tangan."></textarea>
                @error('tindak_lanjut')
                    <span class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="bg-bg-light px-7 py-5 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl gap-3">
            <x-atoms.button variant="secondary" size="md" x-on:click="show = false">Batal</x-atoms.button>
            <x-atoms.button wire:click="save">
                {{ $editingId ? 'Perbarui Alih Tangan Kasus' : 'Simpan Alih Tangan Kasus' }}
            </x-atoms.button>
        </div>
    </div>

    {{-- MODAL PILIH KASUS --}}
    <div x-data="{ showKasusMenu: @entangle('showKasusModal') }" x-show="showKasusMenu"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity"
        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        style="display: none;">
        <div class="bg-white w-full max-w-[600px] rounded-xl shadow-2xl flex flex-col max-h-[80vh] overflow-hidden"
            @click.away="showKasusMenu = false">
            <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0 flex justify-between items-center">
                <div>
                    <h2 class="text-[20px] font-bold text-gray-900 leading-tight">Pilih Kasus</h2>
                    <p class="text-[13px] text-gray-500 mt-0.5">Pilih kasus yang akan dialihkan</p>
                </div>
            </div>

            <div class="px-6 py-5 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">
                <div class="flex gap-3 mb-5">
                    <div class="relative grow">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live="searchKasus" placeholder="Cari nama siswa, judul kasus, atau NIS..."
                            class="w-full border border-gray-200 rounded-md pl-10 pr-3 py-2 text-[13px] focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm">
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    @forelse($this->kasusOptions as $kasus)
                        <div wire:click="selectKasus({{ $kasus['id'] }})"
                            class="flex items-start gap-3 border border-gray-200 rounded-md p-4 cursor-pointer transition-colors
                                {{ $kasus_id == $kasus['id'] ? 'border-teal-400 bg-teal-50' : 'hover:border-primary hover:bg-bg-light' }}">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-[14px] font-bold text-gray-900">{{ $kasus['nama_siswa'] }}</h4>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ match($kasus['prioritas']) {
                                            'Tinggi' => 'bg-red-100 text-red-700',
                                            'Sedang' => 'bg-yellow-100 text-yellow-700',
                                            default => 'bg-green-100 text-green-700',
                                        } }}">
                                        {{ $kasus['prioritas'] }}
                                    </span>
                                </div>
                                <p class="text-[12px] text-gray-600 mt-0.5 font-medium">{{ $kasus['penanganan'] }}</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    {{ $kasus['kategori'] }} &middot; {{ $kasus['kelas_label'] }} &middot; {{ $kasus['tanggal_mulai'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 text-sm">
                            {{ $searchKasus ? 'Tidak ada kasus ditemukan.' : 'Tidak ada kasus yang tersedia untuk dialihkan.' }}
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-2.5">
                <button type="button" wire:click="closeKasusModal"
                    class="px-5 py-2 bg-white border border-gray-200 rounded-md text-[13px] font-bold text-gray-600 hover:bg-gray-50 transition-colors shadow-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    </x-shared.modal>
</div>
