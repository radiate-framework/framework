<?php

namespace Radiate\Validation\Rules;

class IsBoolean implements Rule
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
        return in_array($value, [true, false, 0, 1, 'true', 'false', '0', '1'], true);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return ':Attribute must be true or false';
    }
}
