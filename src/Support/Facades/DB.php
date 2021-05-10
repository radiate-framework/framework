<?php

namespace Radiate\Support\Facades;

class DB extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'db';
    }
}
