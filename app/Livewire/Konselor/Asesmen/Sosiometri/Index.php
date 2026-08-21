<?php

namespace App\Livewire\Konselor\Asesmen\Sosiometri;

use App\Models\DataSiswa;
use App\Services\Asesmen\SosiometriService;
use App\Services\ImportExportService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Index extends Component
{
    use WithFileUploads;

    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    public Collection $records;

    public array $students = [];

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    public bool $showFilters = false;

    public array $selected = [];

    /*
    |--------------------------------------------------------------------------
    | MODAL
    |--------------------------------------------------------------------------
    */

    public bool $showStudentModal = false;

    public ?string $pickerFor = null;

    public ?int $editingId = null;

    #[Url]
    public ?int $edit = null;

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    public string $search = '';

    public string $searchSiswa = '';

    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

    public string $filterKelas = '';

    public string $filterJurusan = '';

    public ?string $selectedTingkat = null;

    public array $kelasOptions = [];

    public array $jurusanOptions = [];

    // ── IMPORT / EXPORT STATE ─────────────────────────
    public bool $showImportModal = false;
    #[Validate('required|file|mimes:csv,xlsx,xls|max:5120')]
    public $importFile = null;
    public int $importedCount = 0;
    public array $importErrors = [];
    public bool $showExportModal = false;
    public int $exportPreviewCount = 0;

    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public $siswa_id = '';

    public $judul = '';

    public $instruksi = '';

    public $jumlah_pilihan = 3;

    public array $pertanyaanJawaban = [];

    public int $step = 1;

    /*
    |--------------------------------------------------------------------------
    | MOUNT
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->records = collect();

        $this->students = DataSiswa::with([
            'user',
            'kelas.jurusan'
        ])
        ->get()
        ->sortBy(fn($student) => $student->nama ?? '')
        ->values()
        ->all();

        $this->loadData();

        $this->loadFilterOptions();

        if ($this->edit) {
            // Find the class string of the student first so we don't end up on a blank list
            $record = app(SosiometriService::class)->findById($this->edit);
            if ($record) {
                // Determine 'tingkat' from 'kelas_label' (e.g. 'X RPL 1' -> 'X')
                $kelas = explode(' ', $record->siswa?->kelas_label ?? '')[0];
                if (in_array($kelas, ['X', 'XI', 'XII'])) {
                    $this->pilihTingkat($kelas);
                }
                $this->loadSosiometri($this->edit);
            }
            $this->edit = null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD DATA
    |--------------------------------------------------------------------------
    */

    public function loadData(): void
    {
        $service = app(SosiometriService::class);

        $this->records = $service->getFiltered([
            'search' => $this->search,
            'kelas' => $this->filterKelas,
            'jurusan' => $this->filterJurusan,
            'tingkat' => $this->selectedTingkat,
        ]);
    }

    public function pilihTingkat(string $tingkat): void
    {
        if (!in_array($tingkat, ['X', 'XI', 'XII'], true)) {
            return;
        }

        $this->selectedTingkat = $tingkat;
        $this->search = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';

        $this->loadData();
    }

    public function kembaliKeTingkat(): void
    {
        $this->selectedTingkat = null;
        $this->search = '';
        $this->filterKelas = '';
        $this->filterJurusan = '';

        $this->records = collect();
    }

    public function goToDetail(int $id)
    {
        return redirect()->route('konselor.asesmen.sosiometri.detail', $id);
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER OPTION
    |--------------------------------------------------------------------------
    */

    public function loadFilterOptions(): void
    {
        $service = app(SosiometriService::class);

        $options = $service->getFilterOptions();

        $this->kelasOptions = $options['kelasOptions'] ?? [];

        $this->jurusanOptions = $options['jurusanOptions'] ?? [];
    }

    /*
    |--------------------------------------------------------------------------
    | FILTER EVENT
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->loadData();
    }

    public function updatedFilterKelas(): void
    {
        $this->loadData();
    }

    public function updatedFilterJurusan(): void
    {
        $this->loadData();
    }

    public function filterAction(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterKelas', 'filterJurusan']);
        $this->loadData();
    }

    #[On('refreshTable')]
    public function refreshTable(): void
    {
        $this->loadData();
    }

    public function nextStep(): void
    {
        $this->validate([
            'siswa_id' => 'required|integer',
        ]);

        // Set judul default jika kosong
        if (empty($this->judul)) {
            $this->judul = 'Sosiometri';
        }
        $this->jumlah_pilihan = 3;

        $this->step = 2;
    }

    public function previousStep(): void
    {
        $this->step = 1;
    }

    public function selectStudent(int $id): void
    {
        $this->siswa_id = $id;
        $this->showStudentModal = false;
        $this->searchSiswa = '';
        $this->pickerFor = null;
    }

    public function openStudentModal(): void
    {
        $this->pickerFor = null;
        $this->searchSiswa = '';
        $this->showStudentModal = true;
    }

    public function openQuestionPicker(string $key): void
    {
        if (!array_key_exists($key, \App\Models\Sosiometri::PERTANYAAN)) {
            return;
        }

        $this->pickerFor = $key;
        $this->searchSiswa = '';
        $this->showStudentModal = true;
    }

    public function closeStudentModal(): void
    {
        $this->showStudentModal = false;
        $this->pickerFor = null;
    }

    public function toggleQuestionStudent(string $key, int $id): void
    {
        if (!array_key_exists($key, \App\Models\Sosiometri::PERTANYAAN)) {
            return;
        }

        $ids = $this->pertanyaanJawaban[$key] ?? [];

        if (in_array($id, $ids, true)) {
            $this->pertanyaanJawaban[$key] = array_values(array_diff($ids, [$id]));
            return;
        }

        if (count($ids) >= (int) $this->jumlah_pilihan) {
            return;
        }

        $ids[] = $id;
        $this->pertanyaanJawaban[$key] = $ids;
    }

    public function getInitials(?string $name): string
    {
        if (!$name) {
            return 'S';
        }

        $words = explode(' ', trim($name));

        if (count($words) >= 2) {
            return strtoupper(
                substr($words[0], 0, 1) .
                substr($words[1], 0, 1)
            );
        }

        return strtoupper(substr($name, 0, 2));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */


    #[On('create-sosiometri')]
    public function createSosiometri(): void
    {

        $this->resetValidation();


        $this->resetForm();


        $this->editingId = null;


        $this->dispatch(
            'open-modal',
            'form-sosiometri'
        );

    }




    /*
    |--------------------------------------------------------------------------
    | EDIT / LOAD
    |--------------------------------------------------------------------------
    */

    #[On('edit-sosiometri')]
    public function loadSosiometri(int $id): void
    {
        $service = app(
            SosiometriService::class
        );


        $this->resetValidation();


        $record = $service->findById($id);


        $this->editingId = $id;


        $this->siswa_id =
            $record->siswa_id;


        $this->judul =
            $record->judul;


        $this->instruksi =
            $record->instruksi;


        $this->jumlah_pilihan =
            $record->jumlah_pilihan;


        $this->pertanyaanJawaban = [];

        foreach (\App\Models\Sosiometri::PERTANYAAN as $key => $pertanyaan) {
            $names = $record->respons
                ->where('pertanyaan', $key)
                ->sortBy('urutan')
                ->map(fn ($r) => $r->nama_dipilih ?? $r->siswaDipilih?->nama ?? '')
                ->filter()
                ->values()
                ->all();
            
            $this->pertanyaanJawaban[$key] = implode(', ', $names);
        }


        $this->step = 2;


        $this->dispatch(
            'open-modal',
            'form-sosiometri'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE
    |--------------------------------------------------------------------------
    */


    public function save(
        SosiometriService $service
    ): void
    {

        $this->validate([
            'siswa_id' => 'required|integer',
            'judul' => 'required|string|max:255',
            'instruksi' => 'nullable|string',
            'jumlah_pilihan' => 'required|integer|min:1|max:10',
            'pertanyaanJawaban' => 'nullable|array',
        ]);


        $data = [

            'siswa_id' =>
                $this->siswa_id,

            'judul' =>
                $this->judul,

            'instruksi' =>
                $this->instruksi,

            'jumlah_pilihan' => (int) $this->jumlah_pilihan,

        ];

        if($this->editingId){

            $sosiometri = $service->update(
                $this->editingId,
                $data
            );


            session()->flash(
                'success',
                'Data sosiometri berhasil diperbarui!'
            );


        }else{


            $sosiometri = $service->create($data);


            session()->flash(
                'success',
                'Data sosiometri berhasil ditambahkan!'
            );

        }

        $this->saveRespons($sosiometri);

        $this->resetForm();


        $this->dispatch(
            'close-modal',
            'form-sosiometri'
        );


        $this->dispatch(
            'refreshTable'
        );


    }





    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */


    /*
    |--------------------------------------------------------------------------
    | SAVE RESPONS
    |--------------------------------------------------------------------------
    */

    protected function saveRespons(\App\Models\Sosiometri $sosiometri): void
    {
        $sosiometri->respons()->delete();

        foreach (\App\Models\Sosiometri::PERTANYAAN as $key => $pertanyaan) {
            $rawInput = $this->pertanyaanJawaban[$key] ?? '';
            $names = array_filter(array_map('trim', explode(',', $rawInput)));
            $names = array_slice($names, 0, 3); // limit to 3

            foreach (array_values($names) as $urutan => $nama) {
                if ($nama === '') continue;

                $sosiometri->respons()->create([
                    'siswa_dipilih_id' => null,
                    'nama_dipilih'     => $nama,
                    'siswa_pemilih_id' => (int) $this->siswa_id,
                    'urutan'           => $urutan + 1,
                    'alasan'           => '',
                    'pertanyaan'       => $key,
                ]);
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */


    public function delete(
        int $id,
        SosiometriService $service
    ): void
    {


        $service->delete($id);



        session()->flash(
            'success',
            'Data sosiometri berhasil dihapus!'
        );



        $this->loadData();


    }






    /*
    |--------------------------------------------------------------------------
    | RESET FORM
    |--------------------------------------------------------------------------
    */
    public function resetForm(): void
    {
        $this->reset([
            'siswa_id',
            'judul',
            'instruksi',
            'pertanyaanJawaban',
            'pickerFor',
        ]);


        $this->showStudentModal = false;


        $this->jumlah_pilihan = 3;


        $this->step = 1;


        $this->editingId = null;
    }

    // ── IMPORT ───────────────────────────────

    public function openImport(): void
    {
        $this->resetValidation('importFile');
        $this->importFile = null;
        $this->importedCount = 0;
        $this->importErrors = [];
        $this->showImportModal = true;
    }

    public function closeImport(): void
    {
        $this->showImportModal = false;
    }

    public function processImport(SosiometriService $service): void
    {
        $this->validate();

        $result = $service->importFromFile($this->importFile);

        $this->importedCount = $result['imported'];
        $this->importErrors = $result['errors'];

        if (empty($this->importErrors)) {
            $this->showImportModal = false;
            session()->flash('success', "{$this->importedCount} data sosiometri berhasil diimport.");
            $this->loadData();
            $this->loadFilterOptions();
        }
    }

    // ── EXPORT ───────────────────────────────

    public function openExport(SosiometriService $service): void
    {
        $this->exportPreviewCount = $service->getExportCount($this->exportFilters());
        $this->showExportModal = true;
    }

    public function closeExport(): void
    {
        $this->showExportModal = false;
    }

    public function exportCsv(SosiometriService $service, ImportExportService $ies): StreamedResponse
    {
        $this->showExportModal = false;

        return $ies->streamCsv('data-sosiometri-'.date('Ymd-His').'.csv', $service->getTemplateHeaders(), $service->exportRows($this->exportFilters()));
    }

    public function exportExcel(SosiometriService $service, ImportExportService $ies): StreamedResponse
    {
        $this->showExportModal = false;

        return $ies->streamExcelExport('data-sosiometri-'.date('Ymd-His').'.xlsx', $service->getTemplateHeaders(), $service->exportRows($this->exportFilters()));
    }

    public function downloadTemplate(SosiometriService $service, ImportExportService $ies): StreamedResponse
    {
        return $ies->streamExcelTemplate('template-sosiometri.xlsx', $service->getTemplateHeaders(), $service->getTemplateSampleRows());
    }

    private function exportFilters(): array
    {
        return [
            'search' => $this->search ?: null,
            'kelas' => $this->filterKelas ?: null,
            'jurusan' => $this->filterJurusan ?: null,
            'tingkat' => $this->selectedTingkat ?: null,
        ];
    }
}