<?php

namespace App\Events\BimbinganIndividu;

use App\Events\Base\DomainEvent;
use App\Models\BimbinganIndividu;

class BimbinganIndividuUpdated extends DomainEvent
{
    public function __construct(
        public readonly BimbinganIndividu $record,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Memperbarui layanan konseling individu untuk siswa {$record->kasus->siswa->user->nama}",
        );
    }
}
