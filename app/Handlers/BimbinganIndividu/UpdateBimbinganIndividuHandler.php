<?php

namespace App\Handlers\BimbinganIndividu;

use App\Constants\FlashMessages;
use App\Events\BimbinganIndividu\BimbinganIndividuUpdated;
use App\Handlers\Contracts\HandlerInterface;
use App\Handlers\Results\HandlerResult;
use App\Models\KasusBk;
use App\Services\e\K\BimbinganIndividuService;
use Illuminate\Support\Facades\DB;

class UpdateBimbinganIndividuHandler implements HandlerInterface
{
    public function __construct(
        protected BimbinganIndividuService $service,
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
                return HandlerResult::fail('Data layanan konseling individu tidak ditemukan.');
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

            $updated = $record->fresh(['guruBk.user', 'tahunAjaran', 'kasus.siswa.user', 'kasus.siswa.kelas.jurusan']);

            return HandlerResult::ok(
                message: FlashMessages::BIMBINGAN_INDIVIDU_UPDATED,
                data: $updated,
                eventClass: BimbinganIndividuUpdated::class,
                eventPayload: ['record' => $updated],
            );
        });
    }
}
