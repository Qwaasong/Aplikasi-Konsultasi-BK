<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Kasus BK
        $this->app->bind(
            \App\Repositories\Contracts\KasusBkRepositoryInterface::class,
            \App\Repositories\Eloquent\KasusBkRepository::class
        );

        // Pengunduran Diri
        $this->app->bind(
            \App\Repositories\Contracts\PengunduranDiriRepositoryInterface::class,
            \App\Repositories\Eloquent\PengunduranDiriRepository::class
        );

        // Kelola Data
        $this->app->bind(
            \App\Repositories\Contracts\SekolahRepositoryInterface::class,
            \App\Repositories\Eloquent\SekolahRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\JurusanRepositoryInterface::class,
            \App\Repositories\Eloquent\JurusanRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\KelasRepositoryInterface::class,
            \App\Repositories\Eloquent\KelasRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\TahunAjaranRepositoryInterface::class,
            \App\Repositories\Eloquent\TahunAjaranRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
