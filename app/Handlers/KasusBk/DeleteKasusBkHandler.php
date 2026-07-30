<?php

namespace App\Handlers\KasusBk;

use App\Constants\FlashMessages;
use App\Events\KasusBk\KasusBkDeleted;
use App\Handlers\Contracts\HandlerInterface;
use App\Handlers\Results\HandlerResult;
use App\Models\KasusBk;
use App\Services\Bk\KasusBkService;
use Illuminate\Support\Facades\DB;

class DeleteKasusBkHandler implements HandlerInterface
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

        return DB::transaction(function () use ($id) {
            $record = KasusBk::with(['siswa.user'])->find($id);
            if (!$record) {
                return HandlerResult::fail('Data kasus BK tidak ditemukan.');
            }

            $siswaName = $record->siswa->user->nama ?? null;

            // Delete associated lampirans (files)
            if ($record->lampirans) {
                foreach ($record->lampirans as $lampiran) {
                    $path = storage_path('app/public/' . $lampiran->file_path);
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    $lampiran->delete();
                }
            }

            // Delete the kasus record
            $this->service->delete($id);

            return HandlerResult::ok(
                message: FlashMessages::KASUS_BK_DELETED,
                eventClass: KasusBkDeleted::class,
                eventPayload: ['recordId' => $id, 'siswaName' => $siswaName],
            );
        });
    }
}
