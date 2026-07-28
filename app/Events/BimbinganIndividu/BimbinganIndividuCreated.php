<?php

namespace App\Events\BimbinganIndividu;

use App\Events\Base\DomainEvent;
use App\Models\BimbinganIndividu;

class BimbinganIndividuCreated extends DomainEvent
{
    public function __construct(
        public readonly BimbinganIndividu $record,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Membuat layanan konseling individu untuk siswa {$record->kasus->siswa->user->nama}",
        );
    }
}
