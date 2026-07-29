<?php

namespace App\Events\BimbinganKelompok;

use App\Events\Base\DomainEvent;

class BimbinganKelompokDeleted extends DomainEvent
{
    public function __construct(
        public readonly int $recordId,
        public readonly ?string $namaKegiatan = null,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Menghapus layanan konseling kelompok" . ($namaKegiatan ? ": {$namaKegiatan}" : ''),
        );
    }
}
