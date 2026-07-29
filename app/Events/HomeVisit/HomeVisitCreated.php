<?php

namespace App\Events\HomeVisit;

use App\Events\Base\DomainEvent;
use App\Models\HomeVisit;

class HomeVisitCreated extends DomainEvent
{
    public function __construct(
        public readonly HomeVisit $record,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Mencatat kunjungan rumah untuk siswa {$record->kasus->siswa->user->nama}",
        );
    }
}
