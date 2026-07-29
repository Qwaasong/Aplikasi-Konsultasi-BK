<?php

namespace App\Listeners;

use App\Events\Base\DomainEvent;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Log;

class LogActivity
{
    public function handle(DomainEvent $event): void
    {
        if (!class_exists(LogAktivitas::class)) {
            return;
        }

        try {
            LogAktivitas::create([
                'user_id' => $event->userId,
                'event_class' => class_basename($event),
                'description' => $event->description,
                'payload' => collect($event)
                    ->except(['userId', 'description'])
                    ->toArray(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log activity', [
                'event' => class_basename($event),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
