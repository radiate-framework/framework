<?php

namespace Radiate\Validation\Rules;

use Radiate\Support\Str;

class IsUuid implements Rule
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
        return Str::isUuid($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return ':Attribute must be a valid UUID';
    }
}
