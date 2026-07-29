<?php

namespace App\Exceptions;

use RuntimeException;

abstract class DomainException extends RuntimeException
{
    abstract public function getErrorCode(): string;

    public function render()
    {
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'error' => $this->getMessage(),
                'code' => $this->getErrorCode(),
            ], $this->getCode() ?: 422);
        }

        return back()->withErrors([
            'error' => $this->getMessage(),
        ])->with('error_code', $this->getErrorCode());
    }
}
