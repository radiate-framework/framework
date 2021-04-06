<?php

namespace Radiate\Http\Client;

use Exception;

class RequestException extends Exception
{
    /**
     * The response instance.
     *
     * @var \Radiate\Http\Client\Response
     */
    public $response;

    /**
     * Create a new exception instance.
     *
     * @param  \Radiate\Http\Client\Response  $response
     * @return void
     */
    public function __construct(Response $response)
    {
        parent::__construct("HTTP request returned status code {$response->status()}.", $response->status());

        $this->response = $response;
    }
}
