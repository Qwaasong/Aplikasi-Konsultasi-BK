<?php

namespace App\Events\KonferensiKasus;

use App\Events\Base\DomainEvent;
use App\Models\KonferensiKasus;

class KonferensiKasusCreated extends DomainEvent
{
    public function __construct(
        public readonly KonferensiKasus $record,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Membuat konferensi kasus: {$record->judul}",
        );
    }
}
