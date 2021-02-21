<?php

namespace Radiate\Validation\Rules;

class IsDigitsBetween implements Rule
{
    /**
     * The min digits
     *
     * @var int
     */
    protected $min;

    /**
     * The max digits
     *
     * @var int
     */
    protected $max;

    /**
     * Create the rule instance
     *
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
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
        if (!is_numeric($value)) {
            return false;
        }

        $length = strlen((string) $value);

        return $length >= $this->min && $length <= $this->max;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return ":Attribute must be between {$this->min} and {$this->max} digits";
    }
}
