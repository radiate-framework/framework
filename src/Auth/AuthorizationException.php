<?php

namespace Radiate\Auth;

use Radiate\Foundation\Http\Exceptions\HttpResponseException;
use Radiate\Http\Response;

class AuthorizationException extends HttpResponseException
{
    /**
     * Create a new authorization exception.
     *
     * @param  string  $message
     * @return void
     */
    public function __construct(string $message = 'This action is unauthorized.', int $code = 403, array $headers = [])
    {
        parent::__construct(new Response($message, $code, $headers));
    }
}
