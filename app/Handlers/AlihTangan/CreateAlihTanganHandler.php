<?php

namespace App\Handlers\AlihTangan;

use App\Constants\FlashMessages;
use App\Events\AlihTangan\AlihTanganCreated;
use App\Handlers\Contracts\HandlerInterface;
use App\Handlers\Results\HandlerResult;
use App\Models\BimbinganIndividu;
use App\Models\BimbinganKelompok;
use App\Models\HomeVisit;
use App\Models\KasusBk;
use App\Models\KonferensiKasus;
use App\Services\l\AlihTanganKasusService;
use App\Services\e\PegawaiService;
use Illuminate\Support\Facades\DB;

class CreateAlihTanganHandler implements HandlerInterface
{
    public function __construct(
        protected AlihTanganKasusService $service,
        protected PegawaiService $pegawaiService,
    ) {}

    public function handle(array $data, array $context = []): HandlerResult
    {
        return DB::transaction(function () use ($data) {
            // 1. Resolve current counselor as the source
            $pegawai = $this->pegawaiService->getCurrentPegawai();
            $data['nama_asal'] = $pegawai?->id;

            // 2. Create the alih tangan record
            $record = $this->service->create($data);

            // 3. Reassign all related records to new guru BK (highest risk operation)
            if ($record->kasus_id) {
                $this->reassignGuruBk($record->kasus_id, $record->nama_penerima);
            }

            $fresh = $record->fresh(['kasus.siswa.user', 'guruBkAsal.user', 'guruBkTujuan.user']);

            return HandlerResult::ok(
                message: FlashMessages::ALIH_TANGAN_CREATED,
                data: $fresh,
                eventClass: AlihTanganCreated::class,
                eventPayload: ['record' => $fresh],
            );
        });
    }

    /**
     * Reassign all related records to the new guru BK.
     * This updates 5 tables in a single transaction.
     */
    private function reassignGuruBk(int $kasusId, int $newGuruBkId): void
    {
        KasusBk::where('id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        BimbinganIndividu::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        BimbinganKelompok::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        HomeVisit::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
        KonferensiKasus::where('kasus_id', $kasusId)->update(['guru_bk_id' => $newGuruBkId]);
    }
}
