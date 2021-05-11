<?php

namespace Radiate\Database;

use wpdb;

class Connection
{
    /**
     * The wpdb instance
     *
     * @var \wpdb
     */
    protected $wpdb;

    /**
     * Create the connection instance
     *
     * @param \wpdb $wpdb
     */
    public function __construct(wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /**
     * Dynamically call a method on the wpdb instance
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters = [])
    {
        return $this->wpdb->$method(...$parameters);
    }

    /**
     * Dynamically get a property from the wpdb instance
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->wpdb->$key;
    }

    /**
     * Dynamically get a property from the wpdb instance
     *
     * @param string $property
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, $value)
    {
        $this->wpdb->$key = $value;
    }

    /**
     * Dynamically check if a property exists on the wpdb instance
     *
     * @param string $key
     * @return bool
     */
    public function __isset(string $key)
    {
        return isset($this->wpdb->$key);
    }

    /**
     * Dynamically unset a property on the wpdb instance
     *
     * @param string $property
     * @return void
     */
    public function __unset(string $key)
    {
        unset($this->wpdb->$key);
    }
}
