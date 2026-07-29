<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\SiswaRepositoryInterface::class,
            \App\Repositories\Eloquent\SiswaRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\PegawaiRepositoryInterface::class,
            \App\Repositories\Eloquent\PegawaiRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\AlihtanganKasusRepositoryInterface::class,
            \App\Repositories\Eloquent\AlihtanganKasusRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\BimbinganKelompokRepositoryInterface::class,
            \App\Repositories\Eloquent\BimbinganKelompokRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\BimbinganKelompokSiswaRepositoryInterface::class,
            \App\Repositories\Eloquent\BimbinganKelompokSiswaRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\KategoriKasusRepositoryInterface::class,
            \App\Repositories\Eloquent\KategoriKasusRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\KehadiranRepositoryInterface::class,
            \App\Repositories\Eloquent\KehadiranRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\KeluargaSiswaRepositoryInterface::class,
            \App\Repositories\Eloquent\KeluargaSiswaRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\KonferensiKasusPesertaRepositoryInterface::class,
            \App\Repositories\Eloquent\KonferensiKasusPesertaRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\KonferensiKasusRepositoryInterface::class,
            \App\Repositories\Eloquent\KonferensiKasusRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\PelanggaranSiswaRepositoryInterface::class,
            \App\Repositories\Eloquent\PelanggaranSiswaRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\SosiometriRepositoryInterface::class,
            \App\Repositories\Eloquent\SosiometriRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\HomeVisitRepositoryInterface::class,
            \App\Repositories\Eloquent\HomeVisitRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\BimbinganIndividuRepositoryInterface::class,
            \App\Repositories\Eloquent\BimbinganIndividuRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\PeminatanRepositoryInterface::class,
            \App\Repositories\Eloquent\PeminatanRepository::class
        );

        // ─── Asesmen ──────────────────────────────────────────
        $this->app->bind(
            \App\Repositories\Contracts\GayaBelajarRepositoryInterface::class,
            \App\Repositories\Eloquent\GayaBelajarRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\AkpdRepositoryInterface::class,
            \App\Repositories\Eloquent\AkpdRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\DcmRepositoryInterface::class,
            \App\Repositories\Eloquent\DcmRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
