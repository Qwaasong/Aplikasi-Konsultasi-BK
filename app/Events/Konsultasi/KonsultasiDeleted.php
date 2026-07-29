<?php

namespace App\Events\Konsultasi;

use App\Events\Base\DomainEvent;

class KonsultasiDeleted extends DomainEvent
{
    public function __construct(
        public readonly int $recordId,
        public readonly ?string $siswaName = null,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Menghapus konsultasi" . ($siswaName ? " untuk siswa {$siswaName}" : ''),
        );
    }
}
