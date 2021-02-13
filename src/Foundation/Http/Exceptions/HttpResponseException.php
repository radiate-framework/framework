<?php

namespace Radiate\Foundation\Http\Exceptions;

use RuntimeException;

class HttpResponseException extends RuntimeException
{
    /**
     * Set the status header and return the message.
     *
     * @return string
     */
    public function getResponse()
    {
        status_header($this->getCode(), $message = $this->getMessage());

        return $message;
    }
}
