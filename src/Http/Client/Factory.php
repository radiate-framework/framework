<?php

namespace Radiate\Http\Client;

class Factory
{
    /**
     * Execute a method against a new pending request instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters = [])
    {
        return (new PendingRequest($this))->{$method}(...$parameters);
    }
}
