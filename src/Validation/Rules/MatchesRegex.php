<?php

namespace Radiate\Validation\Rules;

class MatchesRegex implements Rule
{
    /**
     * The regex pattern
     *
     * @var string
     */
    protected $pattern;

    /**
     * Create the rule instance
     *
     * @param string $pattern
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes(string $attribute, $value): bool
    {
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        return preg_match($this->pattern, $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return ':Attribute format is invalid';
    }
}
