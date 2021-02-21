<?php

namespace Radiate\Validation\Rules;

use Radiate\Support\Facades\Str;

class EndsWith implements Rule
{
    /**
     * The needles to search form
     *
     * @var array
     */
    protected $needles;

    /**
     * Create the rule instance
     *
     * @param mixed ...$needles
     */
    public function __construct(...$needles)
    {
        $this->needles = $needles;
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
        return Str::endsWith($value, $this->needles);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        $needles = implode(', ', $this->needles);

        return ":Attribute must end with one of the following: {$needles}";
    }
}
