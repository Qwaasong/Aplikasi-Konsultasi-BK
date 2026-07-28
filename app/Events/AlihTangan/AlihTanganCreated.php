<?php

namespace App\Events\AlihTangan;

use App\Events\Base\DomainEvent;
use App\Models\AlihtanganKasus;

class AlihTanganCreated extends DomainEvent
{
    public function __construct(
        public readonly AlihtanganKasus $record,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Alih tangan kasus #{$record->kasus_id} dari {$record->nama_asal} ke {$record->nama_penerima}",
        );
    }
}
