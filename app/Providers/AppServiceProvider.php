<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\KasusBkRepositoryInterface::class,
            \App\Repositories\Eloquent\KasusBkRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PengunduranDiriRepositoryInterface::class,
            \App\Repositories\Eloquent\PengunduranDiriRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\PegawaiRepositoryInterface::class,
            \App\Repositories\Eloquent\PegawaiRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
