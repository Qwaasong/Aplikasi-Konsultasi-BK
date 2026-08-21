<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Services\MasterData\TahunAjaranService;

new class extends Component
{
    public bool $editMode = false;
    public ?int $tahunAjaranId = null;

    public string $tahun = '';
    public string $semester = '';
    public bool $status_aktif = false;

    public function rules()
    {
        return [
            'tahun' => ['required', 'string', 'max:20'],
            'semester' => ['required', 'in:Ganjil,Genap'],
            'status_aktif' => ['boolean'],
        ];
    }

    #[On('create-tahun-ajaran')]
    public function create()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->dispatch('open-modal', 'form-tahun-ajaran');
    }

    #[On('edit-tahun-ajaran')]
    public function edit($id)
    {
        $this->resetForm();
        $record = app(TahunAjaranService::class)->findById($id);

        if (!$record) {
            return;
        }

        $this->tahunAjaranId = $record->id;
        $this->tahun = $record->tahun;
        $this->semester = $record->semester;
        $this->status_aktif = (bool) $record->status_aktif;

        $this->editMode = true;
        $this->dispatch('open-modal', 'form-tahun-ajaran');
    }

    public function save()
    {
        $validated = $this->validate();

        if ($validated['status_aktif']) {
            \App\Models\TahunAjaran::query()->update(['status_aktif' => false]);
        }

        if ($this->editMode) {
            app(TahunAjaranService::class)->update($this->tahunAjaranId, $validated);
            session()->flash('success', 'Data tahun ajaran berhasil diperbarui.');
        } else {
            app(TahunAjaranService::class)->create($validated);
            session()->flash('success', 'Data tahun ajaran berhasil ditambahkan.');
        }

        $this->close();
    }

    public function close()
    {
        $this->resetForm();
        $this->dispatch('close-modal', 'form-tahun-ajaran');
    }

    private function resetForm()
    {
        $this->reset(['tahunAjaranId', 'tahun', 'semester', 'status_aktif']);
        $this->resetValidation();
        $this->editMode = false;
    }
};

?>

<x-shared.modal name="form-tahun-ajaran" maxWidth="lg">
    <div class="flex flex-col h-full max-h-[80vh]">

        {{-- HEADER --}}
        <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">
            <h2 class="text-base font-bold text-gray-900 leading-tight">
                {{ $editMode ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $editMode ? 'Perbarui data tahun ajaran' : 'Catat data tahun ajaran baru' }}
            </p>
        </div>

        {{-- BODY --}}
        <div class="px-6 py-4 overflow-y-auto modal-scroll grow" style="scrollbar-width: thin;">
            <div class="space-y-5">
                {{-- Tahun --}}
                <div>
                    <x-atoms.input-label for="tahun" size="sm">
                        Tahun Ajaran <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <x-atoms.text-input id="tahun" wire:model="tahun" size="md" placeholder="e.g., 2025/2026" />
                    @error('tahun')
                        <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Semester --}}
                <div>
                    <x-atoms.input-label for="semester" size="sm">
                        Semester <span class="text-red-500">*</span>
                    </x-atoms.input-label>
                    <select id="semester" wire:model="semester" class="w-full border border-gray-200 rounded-md px-4 py-2 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary">
                        <option value="">Pilih Semester</option>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                    @error('semester')
                        <p class="text-red-500 text-[13px] font-medium mt-1.5 block">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="flex items-center gap-3 pt-2">
                    <input id="status_aktif" type="checkbox" wire:model="status_aktif" class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                    <label for="status_aktif" class="text-sm font-medium text-gray-700">
                        Jadikan Tahun Ajaran Aktif
                    </label>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0 gap-3">
            <x-atoms.button variant="secondary" wire:click="close">
                Batal
            </x-atoms.button>

            <x-atoms.button wire:click="save">
                {{ $editMode ? 'Simpan Perubahan' : 'Tambah Tahun Ajaran' }}
            </x-atoms.button>
        </div>

    </div>
</x-shared.modal>
