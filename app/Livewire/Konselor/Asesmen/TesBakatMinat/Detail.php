<?php

namespace App\Livewire\Konselor\Asesmen\TesBakatMinat;

use App\Models\DataSiswa;
use App\Models\Jurusan;
use App\Models\Peminatan;
use App\Services\Asesmen\PeminatanService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

#[Layout('layouts.app')]
class Detail extends Component
{
    public Peminatan $record;

    public string $search = '';

    /*
    |--------------------------------------------------------------------------
    | EDIT FORM STATE
    |--------------------------------------------------------------------------
    */

    public ?int $editingId = null;

    public $siswa_id = '';

    public $tanggal = '';

    public $hasil = '';

    public $catatan = '';

    public array $jawaban = [];

    public array $students = [];

    public bool $showStudentModal = false;

    public string $searchSiswa = '';

    protected PeminatanService $service;

    public function boot(PeminatanService $service): void
    {
        $this->service = $service;
    }

    public function mount(int $id): void
    {
        $this->record = $this->service->findById($id);
        $this->jawaban = $this->initializeJawaban();

        $this->students = DataSiswa::with(['user', 'kelas.jurusan'])
            ->get()
            ->sortBy(fn ($s) => $s->nama ?? '')
            ->values()
            ->map(fn ($s) => [
                'id'            => $s->id,
                'nama'          => $s->nama ?? '',
                'nis'           => $s->nis ?? '',
                'kelas_label'   => $s->kelas_label ?? '-',
                'jurusan_label' => $s->jurusan_label ?? '-',
            ])
            ->all();
    }

    private function initializeJawaban(): array
    {
        $jawaban = [];
        foreach (Peminatan::SECTIONS as $section) {
            $jawaban[$section] = [];
        }
        return $jawaban;
    }

    #[Computed]
    public function questionGroups(): array
    {
        return $this->record->questionGroups();
    }

    #[Computed]
    public function dominantIntelligences(): array
    {
        return $this->record->dominantIntelligences();
    }

    #[Computed]
    public function skorKecerdasan(): array
    {
        return collect(Peminatan::SECTIONS)
            ->map(fn (string $section) => [
                'section' => $section,
                'skor'    => count($this->jawaban[$section] ?? []),
                'total'   => count(Peminatan::QUESTION_GROUPS[$section] ?? []),
            ])
            ->all();
    }

    #[Computed]
    public function dominantKecerdasan(): string
    {
        $peminatan = new Peminatan;
        $peminatan->jawaban = $this->jawaban;
        return $peminatan->dominantIntelligences()[0] ?? '';
    }

    public function getSearchResultsProperty()
    {
        if (strlen($this->search) < 2) {
            return collect();
        }

        return Peminatan::with('siswa')
            ->whereHas('siswa', function ($query) {
                $query->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('nis', 'like', "%{$this->search}%");
            })
            ->latest()
            ->limit(8)
            ->get();
    }

    public function edit(): void
    {
        $record = $this->record;

        $this->editingId = $record->id;
        $this->siswa_id  = (int) $record->siswa_id;
        $this->tanggal   = optional($record->tanggal)->format('Y-m-d');
        $this->hasil     = $record->hasil;
        $this->catatan   = $record->catatan;

        // Pastikan struktur jawaban lengkap per-section
        $loadedJawaban = $record->jawaban ?? [];
        $jawaban = $this->initializeJawaban();
        foreach (Peminatan::SECTIONS as $section) {
            if (isset($loadedJawaban[$section]) && is_array($loadedJawaban[$section])) {
                $jawaban[$section] = $loadedJawaban[$section];
            }
        }
        $this->jawaban = $jawaban;

        $this->dispatch('open-modal', 'form-peminatan');
    }

    public function save(): void
    {
        $this->validate([
            'siswa_id' => 'required|integer',
            'tanggal'  => 'required|date',
            'hasil'    => 'nullable|string',
            'catatan'  => 'nullable|string',
            'jawaban'  => 'nullable|array',
        ]);

        $peminatan = new Peminatan;
        $peminatan->jawaban = $this->jawaban;
        $dominant = $peminatan->dominantIntelligences();

        $data = [
            'siswa_id' => $this->siswa_id,
            'tanggal'  => $this->tanggal,
            'jawaban'  => $this->jawaban,
            'hasil'    => $dominant[0] !== '' ? $dominant[0] : ($this->hasil ?: ''),
            'catatan'  => $this->catatan,
        ];

        $this->service->update($this->editingId, $data);

        session()->flash('success', 'Data peminatan berhasil diperbarui!');

        $this->dispatch('close-modal', 'form-peminatan');

        // Refresh record dan computed properties
        $this->record = $this->service->findById($this->record->id);
        $this->dispatch('refreshDetail');
    }

    public function selectStudent(int $id): void
    {
        $this->siswa_id = $id;
        $this->showStudentModal = false;
        $this->searchSiswa = '';
    }

    public function openStudentModal(): void
    {
        $this->showStudentModal = true;
    }

    public function closeStudentModal(): void
    {
        $this->showStudentModal = false;
    }

    public function getInitials(?string $name): string
    {
        if (!$name) return 'S';
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }

    public function delete(): void
    {
        $this->service->delete($this->record->id);

        session()->flash('success', 'Data tes bakat minat berhasil dihapus.');

        $this->redirectRoute('konselor.asesmen.tes-bakat-minat.index', navigate: true);
    }

    public function goBack(): void
    {
        $this->redirectRoute('konselor.asesmen.tes-bakat-minat.index', navigate: true);
    }

    protected $listeners = [
        'refreshDetail' => '$refresh',
    ];
}