<?php

namespace Radiate\Validation\Rules;

class IsTimezone implements Rule
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
        return in_array($value, timezone_identifiers_list(), true);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return ':Attribute must be a valid zone.';
    }
}
