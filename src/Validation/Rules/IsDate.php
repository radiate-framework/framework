<?php

namespace Radiate\Validation\Rules;

use DateTimeInterface;

class IsDate implements Rule
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
        if ($value instanceof DateTimeInterface) {
            return true;
        }

        if ((!is_string($value) && !is_numeric($value)) || strtotime($value) === false) {
            return false;
        }

        $date = date_parse($value);

        return checkdate($date['month'], $date['day'], $date['year']);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return ':Attribute is not a valid date';
    }
}
