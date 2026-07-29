<?php

namespace App\Events\BimbinganIndividu;

use App\Events\Base\DomainEvent;

class BimbinganIndividuDeleted extends DomainEvent
{
    public function __construct(
        public readonly int $recordId,
        public readonly ?string $siswaName = null,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Menghapus layanan konseling individu" . ($siswaName ? " untuk siswa {$siswaName}" : ''),
        );
    }
}
