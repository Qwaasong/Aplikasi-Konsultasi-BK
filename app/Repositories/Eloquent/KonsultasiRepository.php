<?php

namespace App\Repositories\Eloquent;

use App\Models\Konsultasi;
use App\Repositories\Contracts\KonsultasiRepositoryInterface;

class KonsultasiRepository implements KonsultasiRepositoryInterface
{
    public function getAll()
    {
        return Konsultasi::with('siswa')->latest()->get();
    }
}
