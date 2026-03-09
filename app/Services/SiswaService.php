<?php

namespace App\Services;

use App\Repositories\Contracts\SiswaRepositoryInterface;

class SiswaService
{
    public function __construct(
        protected SiswaRepositoryInterface $siswaRepository
    ) {
    }

    public function getAll()
    {
        return $this->siswaRepository->getAll();
    }

    public function findById(int $id)
    {
        return $this->siswaRepository->findById($id);
    }

    public function create(array $data)
    {
        return $this->siswaRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->siswaRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->siswaRepository->delete($id);
    }
}
