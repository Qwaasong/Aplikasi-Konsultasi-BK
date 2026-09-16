<?php

namespace App\Livewire\Pages\Siswa;

use App\Models\DataSiswa;
use App\Models\Kehadiran;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

#[Layout('layouts.app', ['title' => 'Absensi Siswa - Bimbingan Konseling'])]
class Absensi extends Component
{
    use WithPagination;

    public ?DataSiswa $siswa = null;
    public string $bulan = '';

    public array $rekap = [
        'Hadir' => 0,
        'Sakit' => 0,
        'Izin' => 0,
        'Alpha' => 0,
    ];

    public function mount(): void
    {
        $user = Auth::user();

        $this->siswa = DataSiswa::with(['user', 'kelas'])->where('user_id', $user->id)->first();

        if (! $this->siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $this->bulan = now()->format('Y-m');
        $this->loadRekap();
    }

    public function updatedBulan(): void
    {
        $this->resetPage();
        $this->loadRekap();
    }

    protected function loadRekap(): void
    {
        $tahunAjaran = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::latest('id')->first();

        if ($tahunAjaran && $this->bulan) {
            $year = substr($this->bulan, 0, 4);
            $month = substr($this->bulan, 5, 2);

            $absensi = Kehadiran::where('siswa_id', $this->siswa->id)
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->whereYear('tanggal_kehadiran', $year)
                ->whereMonth('tanggal_kehadiran', $month)
                ->get();

            $this->rekap['Hadir'] = $absensi->where('status', 'Hadir')->count();
            $this->rekap['Sakit'] = $absensi->where('status', 'Sakit')->count();
            $this->rekap['Izin'] = $absensi->where('status', 'Izin')->count();
            $this->rekap['Alpha'] = $absensi->where('status', 'Alpha')->count();
        } else {
            $this->rekap = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpha' => 0];
        }
    }

    public function exportExcel()
    {
        $tahunAjaran = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::latest('id')->first();
        if (!$tahunAjaran || !$this->bulan) {
            return;
        }

        $year = substr($this->bulan, 0, 4);
        $month = substr($this->bulan, 5, 2);

        $absensi = Kehadiran::where('siswa_id', $this->siswa->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->whereYear('tanggal_kehadiran', $year)
            ->whereMonth('tanggal_kehadiran', $month)
            ->orderBy('tanggal_kehadiran', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $bulanNama = Carbon::createFromFormat('Y-m', $this->bulan)->locale('id')->translatedFormat('F Y');
        $sheet->setTitle('Rekap Absensi');

        // Info rows
        $sheet->setCellValue('A1', 'Rekap Absensi Siswa');
        $sheet->setCellValue('A2', 'Nama: ' . $this->siswa->nama);
        $sheet->setCellValue('A3', 'Kelas: ' . ($this->siswa->kelas_label ?? '-'));
        $sheet->setCellValue('A4', 'Bulan: ' . $bulanNama);

        // Header columns
        $sheet->setCellValue('A6', 'No');
        $sheet->setCellValue('B6', 'Hari');
        $sheet->setCellValue('C6', 'Tanggal');
        $sheet->setCellValue('D6', 'Status Kehadiran');

        $sheet->getStyle('A1:A4')->getFont()->setBold(true);
        $sheet->getStyle('A6:D6')->getFont()->setBold(true);

        // Rekap summary
        $sheet->setCellValue('F1', 'Hadir');
        $sheet->setCellValue('G1', 'Sakit');
        $sheet->setCellValue('H1', 'Izin');
        $sheet->setCellValue('I1', 'Alpha');
        $sheet->setCellValue('F2', $this->rekap['Hadir']);
        $sheet->setCellValue('G2', $this->rekap['Sakit']);
        $sheet->setCellValue('H2', $this->rekap['Izin']);
        $sheet->setCellValue('I2', $this->rekap['Alpha']);
        $sheet->getStyle('F1:I1')->getFont()->setBold(true);

        // Data rows
        $row = 7;
        $no = 1;
        foreach ($absensi as $item) {
            $hari = Carbon::parse($item->tanggal_kehadiran)->locale('id')->translatedFormat('l');
            $tanggal = Carbon::parse($item->tanggal_kehadiran)->format('d-m-Y');

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $hari);
            $sheet->setCellValue('C' . $row, $tanggal);
            $sheet->setCellValue('D' . $row, $item->status);
            $row++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Rekap_Absensi_' . str_replace(' ', '_', $this->siswa->nama) . '_' . $this->bulan . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function render()
    {
        $tahunAjaran = TahunAjaran::where('status_aktif', true)->first() ?? TahunAjaran::latest('id')->first();

        $history = collect();
        if ($tahunAjaran && $this->bulan) {
            $year = substr($this->bulan, 0, 4);
            $month = substr($this->bulan, 5, 2);

            $history = Kehadiran::query()
                ->where('siswa_id', $this->siswa?->id)
                ->where('tahun_ajaran_id', $tahunAjaran->id)
                ->whereYear('tanggal_kehadiran', $year)
                ->whereMonth('tanggal_kehadiran', $month)
                ->orderBy('tanggal_kehadiran', 'asc')
                ->paginate(10);

            $history->getCollection()->transform(function ($item) {
                $item->hari = Carbon::parse($item->tanggal_kehadiran)->locale('id')->translatedFormat('l');
                return $item;
            });
        }

        return view('livewire.pages.siswa.absensi', [
            'history' => $history
        ]);
    }
}
