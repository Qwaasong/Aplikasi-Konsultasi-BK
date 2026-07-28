<?php

namespace App\Exceptions;

class NotFoundException extends DomainException
{
    public function __construct(string $model, int|string $id)
    {
        parent::__construct("Data {$model} dengan ID {$id} tidak ditemukan.", 404);
    }

    public function getErrorCode(): string
    {
        return 'NOT_FOUND';
    }
}
