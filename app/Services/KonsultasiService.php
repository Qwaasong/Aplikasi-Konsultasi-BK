<?php

namespace App\Services;

use App\Repositories\Contracts\KonsultasiRepositoryInterface;

class KonsultasiService
{
    public function __construct(
        protected KonsultasiRepositoryInterface $konsultasiRepository
    ) {
    }

    public function getAll()
    {
        return $this->konsultasiRepository->getAll();
    }
}
