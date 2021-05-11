<?php

namespace Radiate\Database;

class DatabaseManager
{
    /**
     * The database connections
     *
     * @var array
     */
    protected $connections = [];

    /**
     * Create the manager instance
     *
     * @param \Radiate\Database\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connections['wpdb'] = $connection;
    }

    /**
     * Get the connection
     *
     * @param string $name
     * @return \Radiate\Database\Connection
     */
    public function connection(string $name = 'wpdb')
    {
        return $this->connections[$name] ?? $this->connections['wpdb'];
    }

    /**
     * Dynamically call the connection
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }
}
