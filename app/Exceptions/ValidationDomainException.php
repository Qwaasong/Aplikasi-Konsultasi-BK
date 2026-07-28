<?php

namespace App\Exceptions;

class ValidationDomainException extends DomainException
{
    private array $errors;

    public function __construct(string $message, array $errors = [])
    {
        parent::__construct($message, 422);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorCode(): string
    {
        return 'VALIDATION_ERROR';
    }
}
