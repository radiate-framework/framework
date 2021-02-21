<?php

namespace Radiate\Validation\Rules;

class IsDigits implements Rule
{
    /**
     * The digits
     *
     * @var int
     */
    protected $digits;

    /**
     * Create the rule instance
     *
     * @param int $digits
     */
    public function __construct(int $digits)
    {
        $this->digits = $digits;
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
        return is_numeric($value) && strlen((string) $value) == $this->digits;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return ":Attribute must be {$this->digits} digits";
    }
}
