<?php

namespace App\Handlers\AlihTangan;

use App\Constants\FlashMessages;
use App\Events\AlihTangan\AlihTanganUpdated;
use App\Handlers\Contracts\HandlerInterface;
use App\Handlers\Results\HandlerResult;
use App\Models\BimbinganIndividu;
use App\Models\BimbinganKelompok;
use App\Models\HomeVisit;
use App\Models\KasusBk;
use App\Models\KonferensiKasus;
use App\Services\Bk\AlihTanganKasusService;
use Illuminate\Support\Facades\DB;

class UpdateAlihTanganHandler implements HandlerInterface
{
    public function __construct(
        protected AlihTanganKasusService $service,
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
                return HandlerResult::fail('Data alih tangan kasus tidak ditemukan.');
            }

            $penerimaBerubah = isset($data['nama_penerima']) && $data['nama_penerima'] !== $record->nama_penerima;

            $this->service->update($id, $data);

            // Reassign if the recipient changed
            if ($penerimaBerubah && $record->kasus_id) {
                $this->reassignGuruBk($record->kasus_id, $data['nama_penerima']);
            }

            $updated = $record->fresh(['kasus.siswa.user', 'guruBkAsal.user', 'guruBkTujuan.user']);

            return HandlerResult::ok(
                message: FlashMessages::ALIH_TANGAN_UPDATED,
                data: $updated,
                eventClass: AlihTanganUpdated::class,
                eventPayload: ['record' => $updated],
            );
        });
    }

    private function reassignGuruBk(int $kasusId, int $newGuruBkId): void
    {
        KasusBk::where('id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        BimbinganIndividu::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        BimbinganKelompok::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        HomeVisit::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        KonferensiKasus::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
    }
}
