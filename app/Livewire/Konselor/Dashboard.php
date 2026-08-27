<?php

namespace App\Livewire\Konselor;

use App\Models\Akpd;
use App\Models\GayaBelajar;
use App\Models\Peminatan;
use App\Services\Asesmen\AkpdService;
use App\Services\Asesmen\GayaBelajarService;
use App\Services\Asesmen\PeminatanService;
use App\Services\Siswa\SiswaService;
use Livewire\Volt\Component;

class Dashboard extends Component
{
    public function __construct()
    {
        parent::__construct();
    }

    public int $countKelas10 = 0;

    public int $countKelas11 = 0;

    public int $countKelas12 = 0;

    public array $bakatMinatData = [];

    public array $gayaBelajarData = [];

    public array $akpdData = [];

    public function mount(
        SiswaService $siswaService,
        PeminatanService $peminatanService,
        GayaBelajarService $gayaBelajarService,
        AkpdService $akpdService,
    ): void {
        $counts = $siswaService->getCountsByTingkat();

        $this->countKelas10 = $counts['kelas_10'];
        $this->countKelas11 = $counts['kelas_11'];
        $this->countKelas12 = $counts['kelas_12'];

        $peminatanRecords = $peminatanService->getAll();
        $this->bakatMinatData = collect(Peminatan::SECTIONS)
            ->map(fn (string $section) => [
                'label' => 'Kecerdasan '.str_replace('-', ' ', $section),
                'value' => $peminatanRecords->sum(
                    fn (Peminatan $record) => count($record->jawaban[$section] ?? [])
                ),
            ])
            ->all();

        $gayaBelajarRecords = $gayaBelajarService->getAll();
        $this->gayaBelajarData = collect([
            'Visual' => 'visual',
            'Auditorial' => 'auditori',
            'Kinestetik' => 'kinestetik',
        ])->map(fn (string $column, string $label) => [
            'label' => $label,
            'value' => $gayaBelajarRecords->filter(
                fn (GayaBelajar $record) => strtolower((string) $record->hasil) === $column
            )->count(),
        ])->values()->all();

        $akpdRecords = $akpdService->getAll();
        $this->akpdData = collect(Akpd::ASPECT_RANGES)
            ->except('Kesimpulan')
            ->mapWithKeys(function (array $range, string $aspect) use ($akpdRecords): array {
                [$start, $end] = $range;
                $answers = $akpdRecords->flatMap(function (Akpd $record) use ($start, $end): array {
                    return collect(range($start, $end))
                        ->map(fn (int $number) => $record->{'q'.str_pad((string) $number, 2, '0', STR_PAD_LEFT)})
                        ->all();
                });

                return [$aspect => [
                    'ya' => $answers->filter(fn ($answer) => strcasecmp((string) $answer, 'Ya') === 0)->count(),
                    'tidak' => $answers->filter(fn ($answer) => strcasecmp((string) $answer, 'Tidak') === 0)->count(),
                ]];
            })
            ->all();
    }
}
