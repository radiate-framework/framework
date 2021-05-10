<?php

namespace Radiate\Cache;

use ArrayAccess;
use Closure;

class Repository implements ArrayAccess
{
    /**
     * The default number of seconds to store items.
     *
     * @var int
     */
    protected $default = 3600;

    /**
     * The wpdb instance
     *
     * @var object
     */
    protected $wpdb = 3600;

    /**
     * Create the repository instance
     *
     * @param object $wpdb
     */
    public function __construct(object $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush(): bool
    {
        $flush = $this->wpdb->query(
            "DELETE FROM {$this->wpdb->options} WHERE option_name LIKE ('%\_transient\_%')"
        );

        return $flush !== false;
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->flush();
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return bool
     */
    public function forever(string $key, $value): bool
    {
        return $this->put($key, $value,  0);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return $this->delete($key);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return delete_transient($key);
    }

    /**
     * Store an item in the cache if the key does not exist.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $ttl
     * @return bool
     */
    public function add(string $key, $value, int $ttl = 0): bool
    {
        if (!$this->has($key)) {
            return $this->put($key, $value,  $ttl);
        }

        return false;
    }

    /**
     * Store an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $ttl
     * @return bool
     */
    public function put(string $key, $value, int $ttl = 0): bool
    {
        return $this->set($key, $value, $ttl);
    }

    /**
     * Store an item in the cache.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $ttl
     * @return bool
     */
    public function set(string $key, $value, int $ttl = 0): bool
    {
        if ($ttl < 0) {
            return $this->forget($key);
        }

        return set_transient($key, $value, $ttl);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return get_transient($key) ?: $default;
    }

    /**
     * Retrieve an item from the cache and delete it.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function pull(string $key, $default = null)
    {
        $value = $this->get($key, $default);

        $this->forget($key);

        return $value;
    }

    /**
     * Determine if an item exists in the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== false;
    }

    /**
     * Determine if an item doesn't exist in the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public function missing(string $key): bool
    {
        return !$this->has($key);
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     *
     * @param  string  $key
     * @param  int  $ttl
     * @param  \Closure  $callback
     * @return mixed
     */
    public function remember(string $key, int $ttl, Closure $callback)
    {
        $value = $this->get($key);

        if (!is_null($value)) {
            return $value;
        }

        $this->put($key, $value = $callback(), $ttl);

        return $value;
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result forever.
     *
     * @param  string  $key
     * @param  \Closure  $callback
     * @return mixed
     */
    public function rememberForever(string $key, Closure $callback)
    {
        $value = $this->get($key);

        if (!is_null($value)) {
            return $value;
        }

        $this->forever($key, $value = $callback());

        return $value;
    }

    /**
     * Determine if a cached value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Store an item in the cache for the default time.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value, $this->default);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->delete($key);
    }
}