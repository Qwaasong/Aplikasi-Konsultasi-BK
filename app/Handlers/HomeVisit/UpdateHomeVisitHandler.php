<?php

namespace App\Handlers\HomeVisit;

use App\Constants\FlashMessages;
use App\Events\HomeVisit\HomeVisitUpdated;
use App\Handlers\Contracts\HandlerInterface;
use App\Handlers\Results\HandlerResult;
use App\Models\KasusBk;
use App\Services\Bk\HomeVisitService;
use Illuminate\Support\Facades\DB;

class UpdateHomeVisitHandler implements HandlerInterface
{
    public function __construct(
        protected HomeVisitService $service,
    ) {}

    public function handle(array $data, array $context = []): HandlerResult
    {
        $id = $context['id'] ?? null;
        if (!$id) {
            return HandlerResult::fail('ID record tidak ditemukan.');
        }

        return DB::transaction(function () use ($id, $data) {
            $record = $this->service->findById($id);
            if (!$record) {
                return HandlerResult::fail('Data kunjungan rumah tidak ditemukan.');
            }

            unset($data['siswa_id']);

            // Sync penanganan/uraian_masalah/tindak_lanjut to kasus_bk
            if ($record->kasus_id) {
                KasusBk::where('id', $record->kasus_id)->update([
                    'penanganan' => $data['penanganan'] ?? null,
                    'uraian_masalah' => $data['uraian_masalah'] ?? null,
                    'tindak_lanjut' => $data['tindak_lanjut'] ?? null,
                ]);
            }

            // Clean fields already stored in kasus_bk
            unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut']);

            $this->service->update($id, $data);

            $updated = $record->fresh(['kasus.siswa.user', 'kasus.lampirans', 'guruBk.user']);

            return HandlerResult::ok(
                message: FlashMessages::HOME_VISIT_UPDATED,
                data: $updated,
                eventClass: HomeVisitUpdated::class,
                eventPayload: ['record' => $updated],
            );
        });
    }
}
