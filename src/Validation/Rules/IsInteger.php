<?php

namespace Radiate\Validation\Rules;

class IsInteger implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes(string $attribute, $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return ':Attribute must be an integer';
    }
}
