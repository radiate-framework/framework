<?php

namespace Radiate\Validation;

use Radiate\Validation\Rules\IsIn;

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
}
