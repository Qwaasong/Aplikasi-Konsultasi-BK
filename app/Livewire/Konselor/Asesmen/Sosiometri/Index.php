<?php

namespace App\Livewire\Konselor\Asesmen\Sosiometri;

use App\Models\DataSiswa;
use App\Services\SosiometriService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

class Index extends Component
{
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

    public ?int $editingId = null;

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

    public array $kelasOptions = [];

    public array $jurusanOptions = [];

    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public $siswa_id = '';

    public $judul = '';

    public $instruksi = '';

    public $jumlah_pilihan = 3;

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
        ]);
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

        public function nextStep(): void
    {
        $this->validate([
            'siswa_id' => 'required|integer',
            'judul' => 'required|string|max:255',
            'instruksi' => 'nullable|string',
            'jumlah_pilihan' => 'required|integer|min:1|max:10',
        ]);

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


        $this->tanggal = now()
            ->format('Y-m-d');


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


        $this->step = 1;


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
        ]);


        $data = [

            'siswa_id' =>
                $this->siswa_id,

            'judul' =>
                $this->judul,

            'instruksi' =>
                $this->instruksi,

            'jumlah_pilihan' =>
                $this->jumlah_pilihan,

        ];

        if($this->editingId){

            $service->update(
                $this->editingId,
                $data
            );


            session()->flash(
                'success',
                'Data sosiometri berhasil diperbarui!'
            );


        }else{


            $service->create($data);


            session()->flash(
                'success',
                'Data sosiometri berhasil ditambahkan!'
            );

        }

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
        ]);


        $this->jumlah_pilihan = 3;


        $this->step = 1;


        $this->editingId = null;
    }
}