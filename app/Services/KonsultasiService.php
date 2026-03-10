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

    public function findById(int $id)
    {
        return $this->konsultasiRepository->findById($id);
    }

    public function create(array $data): void
    {
        $data['id_user'] = auth()->id() ?? 1; // Fallback to 1 if not logged in (for testing)
        $this->konsultasiRepository->create($data);
    }

    public function update(int $id, array $data): void
    {
        // Add auth check or validation logic if required in the future
        $this->konsultasiRepository->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->konsultasiRepository->delete($id);
    }
}
