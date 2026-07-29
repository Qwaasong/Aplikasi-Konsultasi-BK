<?php

namespace App\Events\Base;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class DomainEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly string $description,
    ) {}

    public function broadcastOn(): array
    {
        return [];
    }
}
