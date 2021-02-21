<?php

namespace Radiate\Support\Facades;

/**
 * @method static \Radiate\Validation\Validator make(array $data, array $rules, array $messages = []) Create a new Validator instance.
 *
 * @see \Radiate\Validation\Factory
 */
class Validator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'validator';
    }
}
