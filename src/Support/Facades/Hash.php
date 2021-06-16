<?php

namespace Radiate\Support\Facades;

/**
 * @method static array info(string $hashedValue) Get information about the given hashed value.
 * @method static string make(string $value, array $options = []) Hash the given value.
 * @method static bool check(string $value, string $hashedValue, array $options = []) Check the given plain value against a hash.
 * @method static bool needsRehash(string $hashedValue, array $options = []) Check if the given hash has been hashed using the given options.
 *
 * @see \Radiate\Hashing\Hasher
 */
class Hash extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'hash';
    }
}
