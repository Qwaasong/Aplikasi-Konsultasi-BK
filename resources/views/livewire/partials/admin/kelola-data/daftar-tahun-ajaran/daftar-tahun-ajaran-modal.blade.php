<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use App\Services\TahunAjaranService;

new class extends Component
{
    public bool $show = false;
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
        $this->show = true;
    }

    #[On('edit-tahun-ajaran')]
    public function edit($id)
    {
        $record = app(TahunAjaranService::class)->findById($id);

        if (!$record) {
            return;
        }

        $this->tahunAjaranId = $record->id;
        $this->tahun = $record->tahun;
        $this->semester = $record->semester;
        $this->status_aktif = (bool) $record->status_aktif;

        $this->editMode = true;
        $this->show = true;
    }

    public function save()
    {
        $validated = $this->validate();

        if ($validated['status_aktif']) {
            \App\Models\TahunAjaran::query()->update([
                'status_aktif' => false
            ]);
        }

        if ($this->editMode) {

            app(TahunAjaranService::class)
                ->update($this->tahunAjaranId, $validated);

            session()->flash(
                'success',
                'Data tahun ajaran berhasil diperbarui.'
            );

        } else {

            app(TahunAjaranService::class)
                ->create($validated);

            session()->flash(
                'success',
                'Data tahun ajaran berhasil ditambahkan.'
            );
        }

        $this->close();
    }

    public function close()
    {
        $this->resetForm();

        $this->show = false;
    }

    private function resetForm()
    {
        $this->reset([
            'tahunAjaranId',
            'tahun',
            'semester',
            'status_aktif',
        ]);

        $this->resetValidation();

        $this->editMode = false;
    }
};

?>

<div>

@if($show)

<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-xl">

        {{-- Header --}}
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold">
                {{ $editMode ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}
            </h2>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-5">

            {{-- Tahun --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Tahun Ajaran
                </label>

                <input
                    type="text"
                    wire:model="tahun"
                    placeholder="2025/2026"
                    class="w-full rounded-lg border-gray-300">

                @error('tahun')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

            </div>

            {{-- Semester --}}
            <div>

                <label class="block text-sm font-medium mb-2">
                    Semester
                </label>

                <select
                    wire:model="semester"
                    class="w-full rounded-lg border-gray-300">

                    <option value="">Pilih Semester</option>
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>

                </select>

                @error('semester')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

            </div>

            {{-- Status --}}
            <div class="flex items-center gap-3">

                <input
                    id="status_aktif"
                    type="checkbox"
                    wire:model="status_aktif"
                    class="rounded border-gray-300 text-brand-teal focus:ring-brand-teal">

                <label
                    for="status_aktif"
                    class="text-sm font-medium text-gray-700">

                    Jadikan Tahun Ajaran Aktif

                </label>

            </div>

        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t flex justify-end gap-3">

            <button
                wire:click="close"
                class="px-4 py-2 rounded-lg border">

                Batal

            </button>

            <button
                wire:click="save"
                class="px-4 py-2 rounded-lg bg-brand-teal text-white">

                {{ $editMode ? 'Simpan Perubahan' : 'Tambah Tahun Ajaran' }}

            </button>

        </div>

    </div>

</div>

@endif

</div>