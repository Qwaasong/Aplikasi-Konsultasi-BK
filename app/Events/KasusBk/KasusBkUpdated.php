<?php

namespace App\Events\KasusBk;

use App\Events\Base\DomainEvent;
use App\Models\KasusBk;

class KasusBkUpdated extends DomainEvent
{
    public function __construct(
        public readonly KasusBk $record,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Memperbarui kasus BK #{$record->id} untuk siswa {$record->siswa->user->nama}",
        );
    }
}
