<?php

namespace App\Events\KonferensiKasus;

use App\Events\Base\DomainEvent;

class KonferensiKasusDeleted extends DomainEvent
{
    public function __construct(
        public readonly int $recordId,
        public readonly ?string $judul = null,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Menghapus konferensi kasus" . ($judul ? ": {$judul}" : ''),
        );
    }
}
