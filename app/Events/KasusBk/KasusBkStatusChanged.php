<?php

namespace App\Events\KasusBk;

use App\Events\Base\DomainEvent;
use App\Models\KasusBk;

class KasusBkStatusChanged extends DomainEvent
{
    public function __construct(
        public readonly KasusBk $record,
        public readonly string $oldStatus,
        public readonly string $newStatus,
    ) {
        parent::__construct(
            userId: auth()->id(),
            description: "Status kasus BK #{$record->id} diubah dari \"{$oldStatus}\" ke \"{$newStatus}\"",
        );
    }
}
