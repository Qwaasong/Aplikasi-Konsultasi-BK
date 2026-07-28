<?php

namespace App\Events\BimbinganKelompok;

use App\Events\Base\DomainEvent;
use App\Models\BimbinganKelompok;

class BimbinganKelompokCreated extends DomainEvent
{
    public function __construct(
        public readonly BimbinganKelompok $record,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Membuat layanan konseling kelompok: {$record->nama_kegiatan}",
        );
    }
}
