<?php

namespace App\Handlers\BimbinganIndividu;

use App\Constants\FlashMessages;
use App\Events\BimbinganIndividu\BimbinganIndividuCreated;
use App\Handlers\Contracts\HandlerInterface;
use App\Handlers\Results\HandlerResult;
use App\Models\KasusBk;
use App\Models\KategoriKasus;
use App\Models\TahunAjaran;
use App\Services\BimbinganIndividuService;
use App\Services\PegawaiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateBimbinganIndividuHandler implements HandlerInterface
{
    public function __construct(
        protected BimbinganIndividuService $service,
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
            $data['tahun_ajaran_id'] ??= TahunAjaran::where('status_aktif', true)->value('id')
                ?? TahunAjaran::latest()->value('id');

            // 2. Extract siswa_id for KasusBk creation
            $siswaId = $data['siswa_id'] ?? null;
            unset($data['siswa_id']);

            // 3. Create KasusBk record
            $kasus = null;
            if ($siswaId) {
                $kasus = KasusBk::create([
                    'siswa_id' => $siswaId,
                    'guru_bk_id' => $pegawai->id,
                    'tahun_ajaran_id' => $data['tahun_ajaran_id'],
                    'kategori_id' => KategoriKasus::inRandomOrder()->value('id'),
                    'penanganan' => $data['penanganan'] ?? 'Konseling Individu',
                    'uraian_masalah' => $data['uraian_masalah'] ?? 'Konseling Individu',
                    'tindak_lanjut' => $data['tindak_lanjut'] ?? null,
                    'status' => 'Open',
                    'prioritas' => 'Sedang',
                    'tanggal_mulai' => $data['tanggal_layanan'] ?? now()->format('Y-m-d'),
                ]);
                $data['kasus_id'] = $kasus->id;
            }

            // 4. Clean fields already stored in kasus_bk
            unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut']);

            // 5. Create BimbinganIndividu record
            $record = $this->service->create($data);

            // 6. Return result with event info
            return HandlerResult::ok(
                message: FlashMessages::BIMBINGAN_INDIVIDU_CREATED,
                data: $record,
                eventClass: BimbinganIndividuCreated::class,
                eventPayload: ['record' => $record],
            );
        });
    }
}
