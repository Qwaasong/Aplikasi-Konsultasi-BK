<?php

namespace App\Events\KasusBk;

use App\Events\Base\DomainEvent;

class KasusBkDeleted extends DomainEvent
{
    public function __construct(
        public readonly int $recordId,
        public readonly ?string $siswaName = null,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Menghapus kasus BK" . ($siswaName ? " untuk siswa {$siswaName}" : ''),
        );
    }
}
