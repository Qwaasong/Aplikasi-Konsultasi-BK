<?php

namespace App\Handlers\KonferensiKasus;

use App\Constants\FlashMessages;
use App\Events\KonferensiKasus\KonferensiKasusUpdated;
use App\Handlers\Contracts\HandlerInterface;
use App\Handlers\Results\HandlerResult;
use App\Services\Bk\KonferensiKasusService;
use Illuminate\Support\Facades\DB;

class UpdateKonferensiKasusHandler implements HandlerInterface
{
    public function __construct(
        protected KonferensiKasusService $service,
    ) {}

    public function handle(array $data, array $context = []): HandlerResult
    {
        $id = $context['id'] ?? null;
        $pesertaData = $context['peserta_data'] ?? [];

        if (! $id) {
            return HandlerResult::fail('ID record tidak ditemukan.');
        }

        return DB::transaction(function () use ($id, $data, $pesertaData) {
            $record = $this->service->findById($id);
            if (! $record) {
                return HandlerResult::fail('Data konferensi kasus tidak ditemukan.');
            }

            $this->service->update($id, $data, $pesertaData);

            $updated = $this->service->findById($id);

            return HandlerResult::ok(
                message: FlashMessages::KONFERENSI_KASUS_UPDATED,
                data: $updated,
                eventClass: KonferensiKasusUpdated::class,
                eventPayload: ['record' => $updated],
            );
        });
    }
}
