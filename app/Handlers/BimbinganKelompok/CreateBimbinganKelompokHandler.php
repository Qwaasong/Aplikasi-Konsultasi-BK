<?php

namespace App\Handlers\BimbinganKelompok;

use App\Constants\FlashMessages;
use App\Events\BimbinganKelompok\BimbinganKelompokCreated;
use App\Handlers\Contracts\HandlerInterface;
use App\Handlers\Results\HandlerResult;
use App\Models\BimbinganKelompokSiswa;
use App\Models\KasusBk;
use App\Models\KategoriKasus;
use App\Models\TahunAjaran;
use App\Services\e\K\BimbinganKelompokService;
use App\Services\e\PegawaiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateBimbinganKelompokHandler implements HandlerInterface
{
    public function __construct(
        protected BimbinganKelompokService $service,
        protected PegawaiService $pegawaiService,
    ) {}

    public function handle(array $data, array $context = []): HandlerResult
    {
        $siswaIds = $context['siswa_ids'] ?? [];

        return DB::transaction(function () use ($data, $siswaIds) {
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

            // 2. Create KasusBk record (use first student as reference)
            $siswaId = !empty($siswaIds) ? $siswaIds[0] : null;
            $kasus = KasusBk::create([
                'siswa_id' => $siswaId,
                'guru_bk_id' => $pegawai->id,
                'tahun_ajaran_id' => $data['tahun_ajaran_id'],
                'kategori_id' => KategoriKasus::inRandomOrder()->value('id'),
                'penanganan' => $data['penanganan'] ?? 'Bimbingan Kelompok',
                'uraian_masalah' => $data['uraian_masalah'] ?? '-',
                'tindak_lanjut' => $data['tindak_lanjut'] ?? null,
                'tanggal_mulai' => $data['tanggal_layanan'] ?? now()->toDateString(),
                'status' => 'Open',
                'prioritas' => 'Sedang',
            ]);
            $data['kasus_id'] = $kasus->id;

            // 3. Clean fields already stored in kasus_bk
            unset($data['penanganan'], $data['uraian_masalah'], $data['tindak_lanjut'], $data['guru_bk_id'], $data['tahun_ajaran_id']);

            // 4. Create BimbinganKelompok record
            $record = $this->service->create($data, $siswaIds);

            // 5. Return result with event info
            return HandlerResult::ok(
                message: FlashMessages::BIMBINGAN_KELOMPOK_CREATED,
                data: $record,
                eventClass: BimbinganKelompokCreated::class,
                eventPayload: ['record' => $record],
            );
        });
    }
}
