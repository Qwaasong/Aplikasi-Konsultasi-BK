<?php

namespace App\Events\AlihTangan;

use App\Events\Base\DomainEvent;
use App\Models\AlihtanganKasus;

class AlihTanganUpdated extends DomainEvent
{
    public function __construct(
        public readonly AlihtanganKasus $record,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Memperbarui alih tangan kasus #{$record->kasus_id}",
        );
    }
}
