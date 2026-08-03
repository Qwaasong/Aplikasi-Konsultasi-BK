<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Daftar binding Interface -> Implementasi Repository.
     * Semua binding repository terpusat di sini.
     */
    public function register(): void
    {
        // ─── Master Data ───────────────────────────────────────
        $this->app->bind(
            \App\Repositories\Contracts\MasterData\SekolahRepositoryInterface::class,
            \App\Repositories\Eloquent\MasterData\SekolahRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\MasterData\JurusanRepositoryInterface::class,
            \App\Repositories\Eloquent\MasterData\JurusanRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\MasterData\KelasRepositoryInterface::class,
            \App\Repositories\Eloquent\MasterData\KelasRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\MasterData\TahunAjaranRepositoryInterface::class,
            \App\Repositories\Eloquent\MasterData\TahunAjaranRepository::class
        );

        // ─── User & Auth ──────────────────────────────────────
        $this->app->bind(
            \App\Repositories\Contracts\User\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\User\UserRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\User\PegawaiRepositoryInterface::class,
            \App\Repositories\Eloquent\User\PegawaiRepository::class
        );

        // ─── Siswa & Keluarga ─────────────────────────────────
        $this->app->bind(
            \App\Repositories\Contracts\Siswa\SiswaRepositoryInterface::class,
            \App\Repositories\Eloquent\Siswa\SiswaRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Siswa\KeluargaSiswaRepositoryInterface::class,
            \App\Repositories\Eloquent\Siswa\KeluargaSiswaRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\User\PengunduranDiriRepositoryInterface::class,
            \App\Repositories\Eloquent\User\PengunduranDiriRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Siswa\KehadiranRepositoryInterface::class,
            \App\Repositories\Eloquent\Siswa\KehadiranRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Siswa\PelanggaranSiswaRepositoryInterface::class,
            \App\Repositories\Eloquent\Siswa\PelanggaranSiswaRepository::class
        );

        // ─── Kasus BK ─────────────────────────────────────────
        $this->app->bind(
            \App\Repositories\Contracts\Bk\KasusBkRepositoryInterface::class,
            \App\Repositories\Eloquent\Bk\KasusBkRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Bk\KategoriKasusRepositoryInterface::class,
            \App\Repositories\Eloquent\Bk\KategoriKasusRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Bk\AlihtanganKasusRepositoryInterface::class,
            \App\Repositories\Eloquent\Bk\AlihtanganKasusRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Bk\KonferensiKasusRepositoryInterface::class,
            \App\Repositories\Eloquent\Bk\KonferensiKasusRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Bk\KonferensiKasusPesertaRepositoryInterface::class,
            \App\Repositories\Eloquent\Bk\KonferensiKasusPesertaRepository::class
        );

        // ─── Bimbingan ────────────────────────────────────────
        $this->app->bind(
            \App\Repositories\Contracts\Bimbingan\BimbinganIndividuRepositoryInterface::class,
            \App\Repositories\Eloquent\Bimbingan\BimbinganIndividuRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Bimbingan\BimbinganKelompokRepositoryInterface::class,
            \App\Repositories\Eloquent\Bimbingan\BimbinganKelompokRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Bimbingan\BimbinganKelompokSiswaRepositoryInterface::class,
            \App\Repositories\Eloquent\Bimbingan\BimbinganKelompokSiswaRepository::class
        );

        // ─── Home Visit / Kunjungan Rumah ─────────────────────
        $this->app->bind(
            \App\Repositories\Contracts\Bk\HomeVisitRepositoryInterface::class,
            \App\Repositories\Eloquent\Bk\HomeVisitRepository::class
        );

        // ─── Asesmen ──────────────────────────────────────────
        $this->app->bind(
            \App\Repositories\Contracts\Asesmen\SosiometriRepositoryInterface::class,
            \App\Repositories\Eloquent\Asesmen\SosiometriRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Asesmen\PeminatanRepositoryInterface::class,
            \App\Repositories\Eloquent\Asesmen\PeminatanRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Asesmen\GayaBelajarRepositoryInterface::class,
            \App\Repositories\Eloquent\Asesmen\GayaBelajarRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Asesmen\AkpdRepositoryInterface::class,
            \App\Repositories\Eloquent\Asesmen\AkpdRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\Asesmen\DcmRepositoryInterface::class,
            \App\Repositories\Eloquent\Asesmen\DcmRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
