<?php

namespace App\Handlers\Contracts;

use App\Handlers\Results\HandlerResult;

interface HandlerInterface
{
    /**
     * Execute the handler with the given data.
     *
     * @param array $data Validated input data
     * @param array $context Additional context (e.g., current user, model instances)
     * @return HandlerResult
     */
    public function handle(array $data, array $context = []): HandlerResult;
}
