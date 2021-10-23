<?php

namespace Radiate\Database;

use RuntimeException;

class ModelNotFoundException extends RuntimeException
{
    /**
     * Create a new model not found exception.
     *
     * @param  string  $message
     * @return void
     */
    public function __construct(string $model)
    {
        parent::__construct("No query results for model [{$model}]", 404);
    }
}
