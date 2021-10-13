<?php

namespace Radiate\Auth;

use Exception;
use Radiate\Foundation\Http\Exceptions\HttpExceptionInterface;

class AuthorizationException extends Exception implements HttpExceptionInterface
{
    /**
     * Create a new authorization exception.
     *
     * @param  string  $message
     * @return void
     */
    public function __construct(string $message = 'This action is unauthorized.', int $code = 403, array $headers = [])
    {
        parent::__construct($message, $code);

        $this->headers = $headers;
    }

    /**
     * Get the exception headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
