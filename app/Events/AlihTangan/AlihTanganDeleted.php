<?php

namespace App\Events\AlihTangan;

use App\Events\Base\DomainEvent;

class AlihTanganDeleted extends DomainEvent
{
    public function __construct(
        public readonly int $recordId,
        public readonly ?int $kasusId = null,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Menghapus data alih tangan kasus" . ($kasusId ? " #{$kasusId}" : ''),
        );
    }
}
