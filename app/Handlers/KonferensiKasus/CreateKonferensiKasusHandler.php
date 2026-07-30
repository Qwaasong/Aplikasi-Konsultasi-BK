<?php

namespace App\Handlers\KonferensiKasus;

use App\Constants\FlashMessages;
use App\Events\KonferensiKasus\KonferensiKasusCreated;
use App\Handlers\Contracts\HandlerInterface;
use App\Handlers\Results\HandlerResult;
use App\Models\KonferensiKasusPeserta;
use App\Services\e\KonferensiKasusService;
use App\Services\e\PegawaiService;
use Illuminate\Support\Facades\DB;

class CreateKonferensiKasusHandler implements HandlerInterface
{
    public function __construct(
        protected KonferensiKasusService $service,
        protected PegawaiService $pegawaiService,
    ) {}

    public function handle(array $data, array $context = []): HandlerResult
    {
        $pesertaData = $context['peserta_data'] ?? [];

        return DB::transaction(function () use ($data, $pesertaData) {
            // 1. Resolve current counselor
            $pegawai = $this->pegawaiService->getCurrentPegawai();
            $data['guru_bk_id'] = $data['guru_bk_id'] ?? $pegawai?->id;

            // 2. Clean fields already stored in kasus_bk
            unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut'], $data['siswa_id']);

            // 3. Create KonferensiKasus record
            $record = $this->service->create($data, $pesertaData);

            // 4. Return result with event info
            return HandlerResult::ok(
                message: FlashMessages::KONFERENSI_KASUS_CREATED,
                data: $record,
                eventClass: KonferensiKasusCreated::class,
                eventPayload: ['record' => $record],
            );
        });
    }
}
