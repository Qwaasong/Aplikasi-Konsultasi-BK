<?php

namespace App\Events\HomeVisit;

use App\Events\Base\DomainEvent;

class HomeVisitDeleted extends DomainEvent
{
    public function __construct(
        public readonly int $recordId,
        public readonly ?string $siswaName = null,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Menghapus kunjungan rumah" . ($siswaName ? " untuk siswa {$siswaName}" : ''),
        );
    }
}
