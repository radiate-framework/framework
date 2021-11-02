<?php

namespace Radiate\Validation;

use Radiate\Validation\Rules\IsIn;
use Radiate\Validation\Rules\IsNotIn;

class Rule
{
    /**
     * Statically call the IsIn rule
     *
     * @param array $array
     * @return \Radiate\Validation\Rules\IsIn
     */
    public static function in(array $array)
    {
        return new IsIn(...$array);
    }

    /**
     * Statically call the IsNotIn rule
     *
     * @param array $array
     * @return \Radiate\Validation\Rules\IsIn
     */
    public static function notIn(array $array)
    {
        return new IsNotIn(...$array);
    }
}
