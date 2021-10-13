<?php

namespace Radiate\Foundation\Http\Exceptions;

interface HttpExceptionInterface
{
    /**
     * Get the exception headers
     *
     * @return array
     */
    public function getHeaders(): array;
}
