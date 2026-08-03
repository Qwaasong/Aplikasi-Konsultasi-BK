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

        $file = $this->csvFile('akpd.csv', ['Timestamp', 'Nama Lengkap', 'Kelas', '1. Q1', '2. Q2', '3. Q3', '4. Q4'], [
            ['01/08/2026 23:44:58', 'Budi Santoso', 'XI RPL 1', 'ya', 'Ya', 'TIDAK', ''],
        ]);

        $result = $service->importFromFile($file);

        $this->assertSame(1, $result['imported']);
        $this->assertSame([], $result['errors']);

        $siswa = DataSiswa::whereHas('user', fn ($q) => $q->where('nama', 'Budi Santoso'))->firstOrFail();
        $akpd = Akpd::where('siswa_id', $siswa->id)->firstOrFail();

        $this->assertSame('Ya', $akpd->q01);
        $this->assertSame('Ya', $akpd->q02);
        $this->assertSame('Tidak', $akpd->q03);
        $this->assertNull($akpd->q04);
    }

    public function test_peminatan_import_gform_maps_statements_and_is_idempotent(): void
    {
        $this->kelas('XI RPL 1');

        $service = app(PeminatanService::class);

        $lg05 = Peminatan::QUESTION_GROUPS['Linguistik']['LG05'];
        $vs01 = Peminatan::QUESTION_GROUPS['Visual-Spasial']['VS01'];

        $file = $this->csvFile('peminatan.csv', ['Timestamp', 'Nama Lengkap', 'Kelas', 'linguistik', 'visual_spasial'], [
            ['01/08/2026 23:44:58', 'Budi Santoso', 'XI RPL 1', $lg05.', '.$vs01, $vs01],
        ]);

        $r1 = $service->importFromFile($file);
        $r2 = $service->importFromFile($file);

        $this->assertSame(1, $r1['imported']);
        $this->assertSame(1, $r2['imported']);
        $this->assertDatabaseCount('peminatans', 1);

        $peminatan = Peminatan::firstOrFail();
        $this->assertContains('LG05', $peminatan->jawaban['Linguistik']);
        $this->assertContains('VS01', $peminatan->jawaban['Visual-Spasial']);
    }

    public function test_gaya_belajar_import_gform_scores_and_factors(): void
    {
        $this->kelas('XI RPL 1');

        $service = app(GayaBelajarService::class);

        $visual1 = GayaBelajar::QUESTION_GROUPS['Visual'][0];
        $visual2 = GayaBelajar::QUESTION_GROUPS['Visual'][1];
        $kinestetik1 = GayaBelajar::QUESTION_GROUPS['Kinestetik'][0];

        $file = $this->csvFile('gaya-belajar.csv', ['Timestamp', 'Nama Lengkap', 'Kelas', $visual1, $visual2, $kinestetik1, 'Gaya belajar yang sesuai dengan saya adalah', 'Faktor apa sajakah yang menghambat belajar saya?', 'Faktor apa sajakah yang mendukung belajar saya?'], [
            ['01/08/2026 23:44:58', 'Budi Santoso', 'XI RPL 1', 'Ya', 'Tidak', 'Ya', 'VISUAL', 'Malas', 'Musik'],
        ]);

        $result = $service->importFromFile($file);

        $this->assertSame(1, $result['imported']);
        $this->assertSame([], $result['errors']);

        $gaya = GayaBelajar::firstOrFail();

        $this->assertSame(1, $gaya->visual);
        $this->assertSame(0, $gaya->auditori);
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
        $this->assertSame([], $result['errors']);

        $dcm = Dcm::firstOrFail();

        $this->assertSame(['A' => ['A01', 'A02'], 'C' => ['C04']], $dcm->jawaban);
        $this->assertTrue(collect($dcm->masalah_teridentifikasi)->contains(fn ($s) => str_starts_with($s, 'A01 -')));
        $this->assertTrue(collect($dcm->masalah_teridentifikasi)->contains(fn ($s) => str_starts_with($s, 'C04 -')));
    }

    public function test_sosiometri_import_gform_creates_header_and_responses(): void
    {
        $kelas = $this->kelas('XI RPL 1');

        // Responden + 2 teman di kelas sama.
        $responden = DataSiswa::factory()->create(['nis' => '12345', 'kelas_id' => $kelas->id]);
        $peerA = DataSiswa::factory()->create(['nis' => '54321', 'kelas_id' => $kelas->id]);
        $peerB = DataSiswa::factory()->create(['nis' => '67890', 'kelas_id' => $kelas->id]);

        $service = app(SosiometriService::class);

        $file = $this->csvFile('sosiometri.csv', ['Timestamp', 'Nama Lengkap', 'Kelas', 'Q1. P1', 'Q2. P2'], [
            ['01/08/2026 23:44:58', $responden->nama, 'XI RPL 1', $peerA->nama.', '.$peerB->nama, ''],
        ]);

        $result = $service->importFromFile($file);

        $this->assertSame(1, $result['imported']);
        $this->assertSame([], $result['errors']);

        $sosiometri = Sosiometri::where('siswa_id', $responden->id)->firstOrFail();
        $this->assertSame(2, $sosiometri->respons()->where('pertanyaan', 'Q1')->count());

        $r1 = SosiometriRespon::where('sosiometri_id', $sosiometri->id)->where('urutan', 1)->firstOrFail();
        $this->assertSame($peerA->id, $r1->siswa_dipilih_id);
    }

    public function test_komulatif_record_import_gform_creates_siswa_and_keluarga(): void
    {
        $this->kelas('X RPL 1');

        $service = app(SiswaService::class);

        $file = $this->csvFile('komulatif.csv', [
            'Timestamp', 'KELAS', 'TAHUN PELAJARAN', 'NAMA LENGKAP', 'JENIS KELAMIN', 'TEMPAT, TANGGAL LAHIR',
            'ASAL SMP', 'AGAMA', 'ALAMAT RUMAH (RT, RW)', 'FOTO DIRI / SELFIE',
            'NAMA LENGKAP AYAH / WALI', 'PENDIDIKAN TERAKHIR AYAH / WALI', 'PEKERJAAN AYAH / WALI', 'NOMOR WA AYAH / WALI', 'ALAMAT RUMAH AYAH / WALI',
            'NAMA LENGKAP IBU / WALI', 'PENDIDIKAN TERAKHIR IBU / WALI', 'PEKERJAAN IBU / WALI', 'NOMOR WA IBU / WALI', 'ALAMAT RUMAH IBU / WALI',
            'STATUS RUMAH', 'LOKASI RUMAH', 'PUNYA KAMAR SENDIRI', 'TRANSPORTASI KE SEKOLAH', 'AKUN MEDIA SOSIAL',
        ], [
            ['29/07/2026 20:50:44', 'X RPL 1', '2026', 'Budi Santoso', 'LAKI-LAKI', 'Malang, 11-07-2010', 'SMP 2 JABUNG', 'Islam', 'JABUNG RT5 RW4', 'https://drive.google.com/foto', 'Hartono', 'SMP', 'Swasta', '0812', 'Kedungboto', 'Yusri', 'SMP', 'IRT', '0857', 'Kedungboto', 'MILIK SENDIRI', 'MASUK GANG', 'YA', 'DIANTAR', 'ig_budi'],
        ]);

        $result = $service->importGformFromFile($file);

        $this->assertSame(1, $result['imported']);
        $this->assertSame([], $result['errors']);

        $siswa = DataSiswa::whereHas('user', fn ($q) => $q->where('nama', 'Budi Santoso'))->firstOrFail();

        $this->assertSame('L', $siswa->user->jenis_kelamin);
        $this->assertSame('Malang', $siswa->tempat_lahir);
        $this->assertSame('2010-07-11', $siswa->tgl_lahir?->format('Y-m-d'));
        $this->assertSame('SMP 2 JABUNG', $siswa->asal_smp);

        $k = KeluargaSiswa::where('siswa_id', $siswa->id)->firstOrFail();
        $this->assertSame('Hartono', $k->nama_ayah);
        $this->assertSame('Yusri', $k->nama_ibu);
        $this->assertSame('0812', $k->nomor_wa_ayah);
        $this->assertSame('0857', $k->nomor_wa_ibu);
        $this->assertTrue($k->punya_kamar_sendiri);
        $this->assertSame('ig_budi', $k->media_sosial);
        $this->assertSame('2026', $k->tahun_pelajaran);

        // Idempotent.
        $r2 = $service->importGformFromFile($file);
        $this->assertSame(1, $r2['imported']);
        $this->assertSame(1, DataSiswa::whereHas('user', fn ($q) => $q->where('nama', 'Budi Santoso'))->count());
    }

    public function test_tes_bakat_minat_index_renders_with_import_export(): void
    {
        $this->actingAs(User::factory()->guruBk()->create());
        $this->kelas('XI RPL 1');

        Volt::test('pages.konselor.asesmen.tes-bakat-minat.index')
            ->assertOk()
            ->assertSee('Import')
            ->assertSee('Export')
            ->assertSee('Template');
    }
}
