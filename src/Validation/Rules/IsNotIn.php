<?php

namespace Radiate\Validation\Rules;

class IsNotIn implements Rule
{
    /**
     * The array to search
     *
     * @var array
     */
    protected $array;

    /**
     * Create the rule instance
     *
     * @param mixed ...$array
     */
    public function __construct(...$array)
    {
        $this->array = $array;
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
        return !in_array($value, $this->array);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return 'The selected :attribute is invalid';
    }
}
