<?php

namespace App\Exceptions;

class AuthorizationException extends DomainException
{
    public function __construct(string $action = 'melakukan aksi ini')
    {
        parent::__construct("Anda tidak memiliki hak akses untuk {$action}.", 403);
    }

    public function getErrorCode(): string
    {
        return 'UNAUTHORIZED';
    }
}
