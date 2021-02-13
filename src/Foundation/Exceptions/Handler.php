<?php

namespace Radiate\Foundation\Exceptions;

use Throwable;
use Radiate\Foundation\Application;
use Radiate\Foundation\Http\Exceptions\HttpResponseException;
use Radiate\Http\Request;

class Handler
{
    /**
     * Create the exception handler
     *
     * @param \Radiate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Render an HTTP exception
     *
     * @param \Radiate\Http\Request $request
     * @param \Throwable $e
     * @return string
     */
    public function render(Request $request, Throwable $e)
    {
        // run through the variations of exceptions to be thrown and handle them
        // either from the exception itself or within the handler.
        // the response should be a string with headers and status set.
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }
        return $e->getMessage();
    }
}
