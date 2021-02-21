<?php

namespace Radiate\Validation\Rules;

class IsMin implements Rule
{
    /**
     * The minimum number
     *
     * @var int
     */
    protected $min;

    /**
     * Create the rule instance
     *
     * @param integer $min
     */
    public function __construct(int $min)
    {
        $this->min = $min;
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
        if (is_numeric($value)) {
            $value = 0 + $value;
        } elseif (is_array($value)) {
            $value = count($value);
        } else {
            $value = mb_strlen($value);
        }

        return $value >= $this->min;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return [
            'numeric' => ":Attribute must be at least {$this->min}",
            'string'  => ":Attribute must be at least {$this->min} characters",
            'array'   => ":Attribute must have at least {$this->min} items",
        ];
    }
}
