<?php

namespace App\Providers;

use App\Listeners\LogActivity;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Bimbingan Individu
        \App\Events\BimbinganIndividu\BimbinganIndividuCreated::class => [LogActivity::class],
        \App\Events\BimbinganIndividu\BimbinganIndividuUpdated::class => [LogActivity::class],
        \App\Events\BimbinganIndividu\BimbinganIndividuDeleted::class => [LogActivity::class],

        // Bimbingan Kelompok
        \App\Events\BimbinganKelompok\BimbinganKelompokCreated::class => [LogActivity::class],
        \App\Events\BimbinganKelompok\BimbinganKelompokUpdated::class => [LogActivity::class],
        \App\Events\BimbinganKelompok\BimbinganKelompokDeleted::class => [LogActivity::class],

        // Home Visit
        \App\Events\HomeVisit\HomeVisitCreated::class => [LogActivity::class],
        \App\Events\HomeVisit\HomeVisitUpdated::class => [LogActivity::class],
        \App\Events\HomeVisit\HomeVisitDeleted::class => [LogActivity::class],

        // Konferensi Kasus
        \App\Events\KonferensiKasus\KonferensiKasusCreated::class => [LogActivity::class],
        \App\Events\KonferensiKasus\KonferensiKasusUpdated::class => [LogActivity::class],
        \App\Events\KonferensiKasus\KonferensiKasusDeleted::class => [LogActivity::class],

        // Alih Tangan Kasus
        \App\Events\AlihTangan\AlihTanganCreated::class => [LogActivity::class],
        \App\Events\AlihTangan\AlihTanganUpdated::class => [LogActivity::class],
        \App\Events\AlihTangan\AlihTanganDeleted::class => [LogActivity::class],

        // Kasus BK
        \App\Events\KasusBk\KasusBkDeleted::class => [LogActivity::class],
        \App\Events\KasusBk\KasusBkUpdated::class => [LogActivity::class],
        \App\Events\KasusBk\KasusBkStatusChanged::class => [LogActivity::class],

        // Konsultasi
        \App\Events\Konsultasi\KonsultasiCreated::class => [LogActivity::class],
        \App\Events\Konsultasi\KonsultasiUpdated::class => [LogActivity::class],
        \App\Events\Konsultasi\KonsultasiDeleted::class => [LogActivity::class],
    ];

    public function boot(): void
    {
        //
    }
}
