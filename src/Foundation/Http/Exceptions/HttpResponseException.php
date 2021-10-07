<?php

namespace Radiate\Foundation\Http\Exceptions;

use Radiate\Http\Response;
use RuntimeException;

class HttpResponseException extends RuntimeException
{
    /**
     * The response
     *
     * @var \Radiate\Http\Response
     */
    protected $response;

    /**
     * Create the exception
     *
     * @param \Radiate\Http\Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get the response
     *
     * @return \Radiate\Http\Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}
