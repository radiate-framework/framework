<?php

namespace Radiate\Validation\Rules;

class TaxonomyExists implements Rule
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
        return taxonomy_exists($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return ':Attribute is invalid';
    }
}
