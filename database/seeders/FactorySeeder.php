<?php

namespace Database\Seeders;

use App\Models\AlihtanganKasus;
use App\Models\BimbinganIndividu;
use App\Models\BimbinganKelompok;
use App\Models\BimbinganKelompokSiswa;
use App\Models\DataSiswa;
use App\Models\HomeVisit;
use App\Models\KasusBk;
use App\Models\Kehadiran;
use App\Models\KeluargaSiswa;
use App\Models\KonferensiKasus;
use App\Models\KonferensiKasusPeserta;
use App\Models\KonsultasiLampiran;
use App\Models\PelanggaranSiswa;
use App\Models\Peminatan;
use App\Models\PengunduranDiri;
use App\Models\Sosiometri;
use App\Models\SosiometriRespon;
use Illuminate\Database\Seeder;

class FactorySeeder extends Seeder
{
    /**
     * Seed semua tabel menggunakan factory.
     *
     * Factory akan auto-create relasi berantai, sehingga setiap baris
     * factory()->create() akan menghasilkan data di tabel-tabel dependen juga.
     *
     * Catatan: Tabel dasar (Sekolah, Jurusan, Kelas, User, Pegawai,
     * KategoriKasus, TahunAjaran) sudah diisi oleh FirstDatabaseSeeder.
     * Factory di sini MENAMBAHKAN data baru di atasnya.
     */
    public function run(): void
    {
        // IDEMPOTEN: Skip jika sudah ada data factory (tidak hanya dari manual seeders)
        if (DataSiswa::count() > 15) {
            $this->command->warn('[Factory] Data factory sudah ada, dilewati. Jalankan "php artisan db:seed --class=FactorySeeder --force" untuk menambah ulang.');
            return;
        }
        // ─────────────────────────────────────────────────────────
        // 1. DATA SISWA TAMBAHAN
        //    (auto-create: User(siswa) + Kelas → Jurusan → Sekolah)
        // ─────────────────────────────────────────────────────────
        $count = DataSiswa::factory()->count(10)->create();
        $this->command->info("[Factory] +{$count->count()} DataSiswa (+ User/Kelas/Jurusan/Sekolah)");

        // ─────────────────────────────────────────────────────────
        // 2. KOMULATIF RECORD / KELUARGA SISWA
        //    (auto-create: DataSiswa → chain penuh)
        // ─────────────────────────────────────────────────────────
        $count = KeluargaSiswa::factory()->count(15)->create();
        $this->command->info("[Factory] +{$count->count()} KeluargaSiswa (Komulatif Record)");

        // ─────────────────────────────────────────────────────────
        // 3. KASUS BK TAMBAHAN
        //    (auto-create: DataSiswa + Pegawai(guru_bk) + TahunAjaran + KategoriKasus)
        // ─────────────────────────────────────────────────────────
        $count = KasusBk::factory()->count(20)->create();
        $this->command->info("[Factory] +{$count->count()} KasusBk (+ DataSiswa/Pegawai/TahunAjaran/Kategori)");

        // ─────────────────────────────────────────────────────────
        // 4. HOME VISIT
        //    (auto-create: KasusBk + Pegawai)
        // ─────────────────────────────────────────────────────────
        $count = HomeVisit::factory()->count(10)->create();
        $this->command->info("[Factory] +{$count->count()} HomeVisit");

        // ─────────────────────────────────────────────────────────
        // 5. BIMBINGAN KELOMPOK
        //    (auto-create: Pegawai + TahunAjaran)
        // ─────────────────────────────────────────────────────────
        $bk = BimbinganKelompok::factory()->count(8)->create();
        $this->command->info("[Factory] +{$bk->count()} BimbinganKelompok");

        // ─────────────────────────────────────────────────────────
        // 6. BIMBINGAN KELOMPOK SISWA (peserta bimbingan)
        //    (auto-create: BimbinganKelompok + DataSiswa)
        // ─────────────────────────────────────────────────────────
        $count = BimbinganKelompokSiswa::factory()->count(20)->create();
        $this->command->info("[Factory] +{$count->count()} BimbinganKelompokSiswa");

        // ─────────────────────────────────────────────────────────
        // 7. BIMBINGAN INDIVIDU
        //    (auto-create: KasusBk + Pegawai + TahunAjaran)
        // ─────────────────────────────────────────────────────────
        $count = BimbinganIndividu::factory()->count(15)->create();
        $this->command->info("[Factory] +{$count->count()} BimbinganIndividu");

        // ─────────────────────────────────────────────────────────
        // 8. KEHADIRAN SISWA
        //    (auto-create: DataSiswa + TahunAjaran)
        // ─────────────────────────────────────────────────────────
        $count = Kehadiran::factory()->count(30)->create();
        $this->command->info("[Factory] +{$count->count()} Kehadiran");

        // ─────────────────────────────────────────────────────────
        // 9. PELANGGARAN SISWA
        //    (auto-create: DataSiswa + KasusBk)
        // ─────────────────────────────────────────────────────────
        $count = PelanggaranSiswa::factory()->count(10)->create();
        $this->command->info("[Factory] +{$count->count()} PelanggaranSiswa");

        // ─────────────────────────────────────────────────────────
        // 10. PENGUNDURAN DIRI
        //    (auto-create: DataSiswa)
        // ─────────────────────────────────────────────────────────
        $count = PengunduranDiri::factory()->count(5)->create();
        $this->command->info("[Factory] +{$count->count()} PengunduranDiri");

        // ─────────────────────────────────────────────────────────
        // 11. SOSIOMETRI
        //    (auto-create: DataSiswa)
        // ─────────────────────────────────────────────────────────
        $sosiometri = Sosiometri::factory()->count(8)->create();
        $this->command->info("[Factory] +{$sosiometri->count()} Sosiometri");

        // ─────────────────────────────────────────────────────────
        // 12. SOSIOMETRI RESPONS
        //    (auto-create: Sosiometri + 2x DataSiswa)
        // ─────────────────────────────────────────────────────────
        $count = SosiometriRespon::factory()->count(20)->create();
        $this->command->info("[Factory] +{$count->count()} SosiometriRespon");

        // ─────────────────────────────────────────────────────────
        // 13. ALIH TANGAN KASUS
        //    (auto-create: KasusBk + 2x Pegawai)
        // ─────────────────────────────────────────────────────────
        $count = AlihtanganKasus::factory()->count(8)->create();
        $this->command->info("[Factory] +{$count->count()} AlihtanganKasus");

        // ─────────────────────────────────────────────────────────
        // 14. KONFERENSI KASUS
        //    (auto-create: KasusBk)
        // ─────────────────────────────────────────────────────────
        $konf = KonferensiKasus::factory()->count(8)->create();
        $this->command->info("[Factory] +{$konf->count()} KonferensiKasus");

        // ─────────────────────────────────────────────────────────
        // 15. KONFERENSI KASUS PESERTA
        //    (auto-create: KonferensiKasus)
        // ─────────────────────────────────────────────────────────
        $count = KonferensiKasusPeserta::factory()->count(20)->create();
        $this->command->info("[Factory] +{$count->count()} KonferensiKasusPeserta");

        // ─────────────────────────────────────────────────────────
        // 16. KONSULTASI LAMPIRAN
        //    (auto-create: KasusBk)
        // ─────────────────────────────────────────────────────────
        $count = KonsultasiLampiran::factory()->count(15)->create();
        $this->command->info("[Factory] +{$count->count()} KonsultasiLampiran");

        // ─────────────────────────────────────────────────────────
        // 17. PEMINATAN SISWA
        //    (auto-create: DataSiswa)
        // ─────────────────────────────────────────────────────────
        $count = Peminatan::factory()->count(15)->create();
        $this->command->info("[Factory] +{$count->count()} Peminatan");

        // ─────────────────────────────────────────────────────────
        // 18. GAYA BELAJAR
        // ─────────────────────────────────────────────────────────
        $count = GayaBelajar::factory()->count(10)->create();
        $this->command->info("[Factory] +{$count->count()} Gaya Belajar");

        // ─────────────────────────────────────────────────────────
        // 19. AKPD
        // ─────────────────────────────────────────────────────────
        $count = Akpd::factory()->count(10)->create();
        $this->command->info("[Factory] +{$count->count()} AKPD");

        // ─────────────────────────────────────────────────────────
        // 20. DCM
        // ─────────────────────────────────────────────────────────
        $count = Dcm::factory()->count(10)->create();
        $this->command->info("[Factory] +{$count->count()} DCM");
    }
}
