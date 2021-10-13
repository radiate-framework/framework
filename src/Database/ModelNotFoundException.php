<?php

namespace Radiate\Database;

use Radiate\Foundation\Http\Exceptions\HttpResponseException;
use Radiate\Http\Response;

class ModelNotFoundException extends HttpResponseException
{
    /**
     * Create a new model not found exception.
     *
     * @param  string  $message
     * @return void
     */
    public function __construct(string $model)
    {
        parent::__construct(
            new Response("No query results for model [{$model}]", 404)
        );
    }
}
