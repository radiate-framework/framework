<?php

namespace Radiate\Http\Client;

class Pool
{
    /**
     * The factory instance.
     *
     * @var \Radiate\Http\Client\Factory
     */
    protected $factory;

    /**
     * The pool of requests.
     *
     * @var array
     */
    protected $pool = [];

    /**
     * Create a new requests pool.
     *
     * @param  \Radiate\Http\Client\Factory|null  $factory
     * @return void
     */
    public function __construct(?Factory $factory = null)
    {
        $this->factory = $factory ?: new Factory();
    }

    /**
     * Add a request to the pool with a key.
     *
     * @param  string  $key
     * @return \Radiate\Http\Client\PendingRequest
     */
    public function as(string $key)
    {
        return $this->pool[$key] = $this->asyncRequest();
    }

    /**
     * Retrieve a new async pending request.
     *
     * @return \Radiate\Http\Client\PendingRequest
     */
    public function asyncRequest()
    {
        return $this->factory->async();
    }

    /**
     * Retrieve the requests in the pool.
     *
     * @return array
     */
    public function getRequests(): array
    {
        return $this->pool;
    }

    /**
     * Add a request to the pool with a numeric index.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return \Radiate\Http\Client\PendingRequest
     */
    public function __call(string $method, array $parameters = [])
    {
        return $this->pool[] = $this->asyncRequest()->$method(...$parameters);
    }
}
