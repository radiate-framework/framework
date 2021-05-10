<?php

namespace Radiate\Support\Facades;

/**
 * @method static bool add(string $key, $value, int $ttl = 0)
 * @method static bool flush()
 * @method static bool clear()
 * @method static bool forever(string $key, $value)
 * @method static bool forget(string $key)
 * @method static bool delete(string $key)
 * @method static bool has(string $key)
 * @method static bool missing(string $key)
 * @method static bool set(string $key, $value, int $ttl = 0)
 * @method static bool put(string $key, $value, int $ttl = 0)
 * @method static mixed get(string $key, mixed $default = null)
 * @method static mixed pull(string $key, mixed $default = null)
 * @method static mixed remember(string $key, int $ttl, \Closure $callback)
 * @method static mixed rememberForever(string $key, \Closure $callback)
 *
 * @see \Radiate\Cache\Repository
 */
class Cache extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'cache';
    }
}
