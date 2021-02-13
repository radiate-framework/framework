<?php

namespace Radiate\Container;

use ArrayAccess;
use Closure;
use Psr\Container\ContainerInterface;

class Container implements ArrayAccess, ContainerInterface
{
    /**
     * The container instance
     *
     * @var \Radiate\Container\Container
     */
    protected static $instance;

    /**
     * The registered bindings
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * The bound instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Register an existing instance in the container.
     *
     * @param string $abstract
     * @param mixed $concrete
     * @return void
     */
    public function instance(string $abstract, $concrete)
    {
        $this->instances[$abstract] = $concrete;
    }

    /**
     * Register a binding with the container.
     *
     * @param string $abstract
     * @param \Closure $concrete
     * @param bool $shared
     * @return void
     */
    public function bind(string $abstract, Closure $concrete, bool $shared = false)
    {
        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    /**
     * Register a shared binding with the container.
     *
     * @param string $abstract
     * @param \Closure $concrete
     * @return void
     */
    public function singleton(string $abstract, $concrete)
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Determine if the abstract is bound to the container
     *
     * @param string $abstract
     * @return bool
     */
    public function has($abstract)
    {
        return isset($this->instances[$abstract]) || isset($this->bindings[$abstract]);
    }

    /**
     * Resolve an instance
     *
     * @param string $abstract
     * @return mixed
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get($abstract)
    {
        try {
            if (isset($this->instances[$abstract])) {
                return $this->instances[$abstract];
            }

            if (isset($this->bindings[$abstract])) {
                if ($this->bindings[$abstract]['shared']) {
                    return $this->instances[$abstract] = $this->bindings[$abstract]['concrete']($this);
                }

                return $this->bindings[$abstract]['concrete']($this);
            }
        } catch (ContainerException $e) {
            if ($this->has($abstract)) {
                throw $e;
            }

            throw new NotFoundException($abstract, $e->getCode(), $e);
        }
    }

    /**
     * Get the container instance
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Set the container instance
     *
     * @param \Radiate\Container\Container $container
     * @return static
     */
    public static function setInstance(Container $container = null)
    {
        return static::$instance = $container;
    }

    /**
     * Determine if an instance exists
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get an instance
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set an instance
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->bind($key, $value instanceof Closure ? $value : function () use ($value) {
            return $value;
        });
    }

    /**
     * Unset any instances or bindings
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->bindings[$key], $this->instances[$key]);
    }

    /**
     * Get an instance
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Set an instance
     *
     * @param string $key
     * @return mixed
     */
    public function __set(string $key, $value)
    {
        $this[$key] = $value;
    }
}
