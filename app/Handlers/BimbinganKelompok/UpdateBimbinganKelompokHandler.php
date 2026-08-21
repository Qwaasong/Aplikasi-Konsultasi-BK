<?php

namespace App\Handlers\BimbinganKelompok;

use App\Constants\FlashMessages;
use App\Events\BimbinganKelompok\BimbinganKelompokUpdated;
use App\Handlers\Contracts\HandlerInterface;
use App\Handlers\Results\HandlerResult;
use App\Services\Bimbingan\BimbinganKelompokService;
use Illuminate\Support\Facades\DB;

class UpdateBimbinganKelompokHandler implements HandlerInterface
{
    public function __construct(
        protected BimbinganKelompokService $service,
    ) {}

    public function handle(array $data, array $context = []): HandlerResult
    {
        $id = $context['id'] ?? null;
        $siswaIds = $context['siswa_ids'] ?? [];

        if (! $id) {
            return HandlerResult::fail('ID record tidak ditemukan.');
        }

        return DB::transaction(function () use ($id, $data, $siswaIds) {
            $record = $this->service->findById($id);
            if (! $record) {
                return HandlerResult::fail('Data layanan konseling kelompok tidak ditemukan.');
            }

            $this->service->update($id, $data, $siswaIds);

            $updated = $this->service->findById($id);

            return HandlerResult::ok(
                message: FlashMessages::BIMBINGAN_KELOMPOK_UPDATED,
                data: $updated,
                eventClass: BimbinganKelompokUpdated::class,
                eventPayload: ['record' => $updated],
            );
        });
    }
}
