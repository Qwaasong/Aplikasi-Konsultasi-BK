<?php

namespace Tests\Feature;

use App\Models\Akpd;
use App\Models\DataSiswa;
use App\Models\Dcm;
use App\Models\GayaBelajar;
use App\Models\Kelas;
use App\Models\KeluargaSiswa;
use App\Models\Peminatan;
use App\Models\Sosiometri;
use App\Models\SosiometriRespon;
use App\Models\User;
use App\Services\Asesmen\AkpdService;
use App\Services\Asesmen\DcmService;
use App\Services\Asesmen\GayaBelajarService;
use App\Services\Asesmen\PeminatanService;
use App\Services\Asesmen\SosiometriService;
use App\Services\Siswa\SiswaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AsesmenImportExportTest extends TestCase
{
    use RefreshDatabase;

    private function kelas(string $nama = 'XI RPL 1'): Kelas
    {
        return Kelas::factory()->create(['nama_kelas' => $nama, 'tingkat' => 'XI']);
    }

    private function csvFile(string $name, array $headers, array $rows): UploadedFile
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return UploadedFile::fake()->createWithContent($name, $content);
    }

    public function test_akpd_import_gform_normalizes_ya_tidak_and_creates_siswa(): void
    {
        $this->kelas('XI RPL 1');
        $service = app(AkpdService::class);

        $file = $this->csvFile('akpd.csv', ['Timestamp', 'Nama Siswa', 'Kelas', '1. Q1', '2. Q2', '3. Q3', '4. Q4'], [
            ['01/08/2026 23:44:58', 'Budi Santoso', 'XI RPL 1', 'ya', 'Ya', 'TIDAK', ''],
        ]);

        $result = $service->importFromFile($file);

        $this->assertSame(1, $result['imported']);
        $this->assertSame([], $result['errors']);
        $this->assertDatabaseHas('akpds', ['q01' => 'Ya', 'q02' => 'Ya', 'q03' => 'Tidak', 'q04' => null]);
    }

    public function test_peminatan_import_gform_maps_long_headers_and_is_idempotent(): void
    {
        $this->kelas('XI RPL 1');
        $service = app(PeminatanService::class);

        $headers = $service->getTemplateHeaders();
        $sample = $service->getTemplateSampleRows()[0];

        $file = $this->csvFile('peminatan.csv', $headers, [$sample]);

        $r1 = $service->importFromFile($file);
        $r2 = $service->importFromFile($file);

        $this->assertSame(1, $r1['imported']);
        $this->assertSame(1, $r2['imported']);
        $this->assertDatabaseCount('peminatans', 1);

        $peminatan = Peminatan::firstOrFail();
        $this->assertContains('NA02', $peminatan->jawaban['Naturalis']);
        $this->assertSame('Naturalis', $peminatan->hasil);
    }

    public function test_gaya_belajar_import_gform_scores_from_section_cells(): void
    {
        $this->kelas('XI RPL 1');
        $service = app(GayaBelajarService::class);
        $headers = $service->getTemplateHeaders();
        $sample = $service->getTemplateSampleRows()[0];

        $file = $this->csvFile('gaya-belajar.csv', $headers, [$sample]);
        $result = $service->importFromFile($file);

        $this->assertSame(1, $result['imported']);
        $gaya = GayaBelajar::firstOrFail();
        $this->assertSame(1, $gaya->visual);
        $this->assertSame(1, $gaya->auditori);
        $this->assertSame(1, $gaya->kinestetik);
        $this->assertSame('VISUAL', $gaya->hasil);
        $this->assertSame('Malas', $gaya->faktor_penghambat);
        $this->assertSame('Musik', $gaya->faktor_pendukung);
    }

    public function test_dcm_import_gform_parses_section_codes(): void
    {
        $this->kelas('X RPL 1');
        $service = app(DcmService::class);

        $file = $this->csvFile('dcm.csv', ['Timestamp', 'NAMA', 'KELAS', 'A. Masalah Kesehatan', 'C. MASALAH KELUARGA'], [
            ['30/07/2026 10:27:39', 'Budi Santoso', 'X RPL 1', 'A01 Sering sakit ketika SD, A02 Sering sakit sekarang', 'C04 Saya adalah tidak ber-ayah'],
        ]);

        $result = $service->importFromFile($file);

        $this->assertSame(1, $result['imported']);
        $dcm = Dcm::firstOrFail();
        $this->assertSame(['A' => ['A01', 'A02'], 'C' => ['C04']], $dcm->jawaban);
    }

    public function test_sosiometri_import_gform_creates_header_and_responses(): void
    {
        $kelas = $this->kelas('XI RPL 1');
        $responden = DataSiswa::factory()->create(['nis' => '12345', 'kelas_id' => $kelas->id]);
        $peerA = DataSiswa::factory()->create(['nis' => '54321', 'kelas_id' => $kelas->id]);

        $service = app(SosiometriService::class);
        $file = $this->csvFile('sosiometri.csv', ['Timestamp', 'Nama Lengkap', 'Kelas', 'Q1. P1', 'Q2. P2'], [
            ['01/08/2026 23:44:58', $responden->nama, 'XI RPL 1', $peerA->nama.', ', ''],
        ]);

        $result = $service->importFromFile($file);

        $this->assertSame(1, $result['imported']);
        $sosiometri = Sosiometri::where('siswa_id', $responden->id)->firstOrFail();
        $this->assertDatabaseHas('sosiometri_respons', ['sosiometri_id' => $sosiometri->id, 'siswa_dipilih_id' => $peerA->id]);
    }

    public function test_komulatif_record_import_gform_creates_siswa_and_keluarga(): void
    {
        $this->kelas('X RPL 1');
        $service = app(SiswaService::class);
        $file = $this->csvFile('komulatif.csv', $service->getGformTemplateHeaders(), [
            ['29/07/2026 20:50:44', 'X RPL 1', '2026', 'Budi Santoso', 'LAKI-LAKI', 'Malang, 11-07-2010', 'SMP 2 JABUNG', 'Islam', 'JABUNG RT5 RW4', 'https://drive.google.com/foto', 'Hartono', 'SMP', 'Swasta', '0812', 'Kedungboto', 'Yusri', 'SMP', 'IRT', '0857', 'Kedungboto', 'MILIK SENDIRI', 'MASUK GANG', 'YA', 'DIANTAR', 'ig_budi'],
        ]);

        $result = $service->importGformFromFile($file);

        $this->assertSame(1, $result['imported']);
        $siswa = DataSiswa::whereHas('user', fn ($q) => $q->where('nama', 'Budi Santoso'))->firstOrFail();
        $this->assertSame('2010-07-11', $siswa->tgl_lahir?->format('Y-m-d'));
        $this->assertDatabaseHas('komulatif_record', ['siswa_id' => $siswa->id, 'nama_ayah' => 'Hartono', 'media_sosial' => 'ig_budi']);
    }

    public function test_asesmen_livewire_index_pages_render_with_import_export(): void
    {
        $this->actingAs(User::factory()->guruBk()->create());
        $this->kelas();

        $pages = [
            'pages.konselor.asesmen.tes-bakat-minat.index',
            'pages.konselor.asesmen.gaya-belajar.index',
            'pages.konselor.asesmen.akpd.index',
            'pages.konselor.asesmen.dcm.index',
            'pages.konselor.asesmen.sosiometri.index',
        ];

        foreach ($pages as $page) {
            Volt::test($page)->assertOk()->assertSee('Import')->assertSee('Export')->assertSee('Template');
        }
    }
}
