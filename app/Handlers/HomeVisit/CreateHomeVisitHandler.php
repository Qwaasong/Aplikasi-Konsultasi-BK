<?php

namespace App\Handlers\HomeVisit;

use App\Constants\FlashMessages;
use App\Events\HomeVisit\HomeVisitCreated;
use App\Handlers\Contracts\HandlerInterface;
use App\Handlers\Results\HandlerResult;
use App\Models\KasusBk;
use App\Models\KategoriKasus;
use App\Services\HomeVisitService;
use App\Services\PegawaiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateHomeVisitHandler implements HandlerInterface
{
    public function __construct(
        protected HomeVisitService $service,
        protected PegawaiService $pegawaiService,
    ) {}

    public function handle(array $data, array $context = []): HandlerResult
    {
        return DB::transaction(function () use ($data) {
            // 1. Resolve current counselor
            $pegawai = $this->pegawaiService->getCurrentPegawai();
            if (!$pegawai) {
                throw ValidationException::withMessages([
                    'guru_bk' => 'Data pegawai tidak ditemukan.',
                ]);
            }

            $data['guru_bk_id'] = $pegawai->id;

            // 2. Extract siswa_id for KasusBk creation
            $siswaId = $data['siswa_id'] ?? null;
            unset($data['siswa_id']);

            // 3. Create KasusBk record
            $kasus = null;
            if ($siswaId) {
                $kasus = KasusBk::create([
                    'siswa_id' => $siswaId,
                    'guru_bk_id' => $pegawai->id,
                    'kategori_id' => KategoriKasus::inRandomOrder()->value('id'),
                    'penanganan' => $data['penanganan'] ?? 'Kunjungan Rumah',
                    'uraian_masalah' => $data['uraian_masalah'] ?? '-',
                    'tindak_lanjut' => $data['tindak_lanjut'] ?? null,
                    'tanggal_mulai' => $data['tanggal_kunjungan'] ?? now()->toDateString(),
                    'status' => 'Open',
                    'prioritas' => 'Sedang',
                ]);
                $data['kasus_id'] = $kasus->id;
            }

            // 4. Clean fields already stored in kasus_bk
            unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut']);

            // 5. Create HomeVisit record
            $record = $this->service->create($data);

            // 6. Return result with event info
            return HandlerResult::ok(
                message: FlashMessages::HOME_VISIT_CREATED,
                data: $record,
                eventClass: HomeVisitCreated::class,
                eventPayload: ['record' => $record],
            );
        });
    }
}
