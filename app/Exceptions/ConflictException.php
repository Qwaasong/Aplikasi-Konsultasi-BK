<?php

namespace App\Exceptions;

class ConflictException extends DomainException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 409);
    }

    public function getErrorCode(): string
    {
        return 'CONFLICT';
    }
}
