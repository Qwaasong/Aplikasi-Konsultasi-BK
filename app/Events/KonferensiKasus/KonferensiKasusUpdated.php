<?php

namespace App\Events\KonferensiKasus;

use App\Events\Base\DomainEvent;
use App\Models\KonferensiKasus;

class KonferensiKasusUpdated extends DomainEvent
{
    public function __construct(
        public readonly KonferensiKasus $record,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Memperbarui konferensi kasus: {$record->judul}",
        );
    }
}
