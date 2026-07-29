<?php

namespace App\Events\Konsultasi;

use App\Events\Base\DomainEvent;
use App\Models\KasusBk;

class KonsultasiUpdated extends DomainEvent
{
    public function __construct(
        public readonly KasusBk $record,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Memperbarui konsultasi untuk siswa {$record->siswa->user->nama}",
        );
    }
}
