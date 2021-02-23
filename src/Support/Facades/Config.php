<?php

namespace Radiate\Support\Facades;

/**
 * @method static bool has($key) Determine if the given configuration value exists.
 * @method static mixed get($key, $default = null) Get the specified configuration value.
 * @method static array all() Get all of the configuration items for the application.
 * @method static void set($key, $value = null) Set a given configuration value.
 * @method static void prepend($key, $value) Prepend a value onto an array configuration value.
 * @method static void push($key, $value) Push a value onto an array configuration value.
 *
 * @see \Radiate\Config\Repository
 */
class Config extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'config';
    }
}
