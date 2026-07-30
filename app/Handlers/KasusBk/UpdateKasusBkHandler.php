<?php

namespace App\Handlers\KasusBk;

use App\Constants\FlashMessages;
use App\Events\KasusBk\KasusBkUpdated;
use App\Handlers\Contracts\HandlerInterface;
use App\Handlers\Results\HandlerResult;
use App\Models\KasusBk;
use App\Services\e\KasusBkService;
use Illuminate\Support\Facades\DB;

class UpdateKasusBkHandler implements HandlerInterface
{
    public function __construct(
        protected KasusBkService $service,
    ) {}

    public function handle(array $data, array $context = []): HandlerResult
    {
        $id = $context['id'] ?? null;
        if (!$id) {
            return HandlerResult::fail('ID kasus tidak ditemukan.');
        }

        return DB::transaction(function () use ($id, $data) {
            $record = KasusBk::find($id);
            if (!$record) {
                return HandlerResult::fail('Data kasus BK tidak ditemukan.');
            }

            // Update the kasus
            $this->service->update($id, $data);

            $updated = $record->fresh(['siswa.user', 'guruBk.user', 'kategori', 'lampirans']);

            return HandlerResult::ok(
                message: FlashMessages::KASUS_BK_UPDATED,
                data: $updated,
                eventClass: KasusBkUpdated::class,
                eventPayload: ['record' => $updated],
            );
        });
    }
}
