<?php

namespace App\Handlers\Results;

class HandlerResult
{
    private function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
        public readonly ?object $data = null,
        public readonly ?string $eventClass = null,
        public readonly array $eventPayload = [],
        public readonly ?array $errors = null,
    ) {}

    public static function ok(
        string $message,
        ?object $data = null,
        ?string $eventClass = null,
        array $eventPayload = [],
    ): self {
        return new self(
            success: true,
            message: $message,
            data: $data,
            eventClass: $eventClass,
            eventPayload: $eventPayload,
        );
    }

    public static function fail(
        string $message,
        ?array $errors = null,
    ): self {
        return new self(
            success: false,
            message: $message,
            errors: $errors,
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}
