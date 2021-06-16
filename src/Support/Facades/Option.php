<?php

namespace Radiate\Support\Facades;

use Radiate\Database\Option as DatabaseOption;

/**
 * @method static bool has(string $key) Determine if the option exists
 * @method static mixed get(string $key, mixed|null $default = null) Get an option
 * @method static array getMany(array $keys, mixed|null $default = null) Get multiple options
 * @method static bool set(string $key, mixed $value) Set an option
 * @method static bool setMany(array $options) Set multiple options
 * @method static bool delete(string $key) Delete an option
 * @method static bool deleteMany(array $keys) Delete multiple options
 * @method static array all() Get all the options
 *
 * @see \Radiate\Database\Option
 */
class Option extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return DatabaseOption::class;
    }
}
