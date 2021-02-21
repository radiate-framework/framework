<?php

namespace Radiate\Validation\Rules;

class IsBetween implements Rule
{
    /**
     * The minimum number
     *
     * @var int
     */
    protected $min;

    /**
     * The maximum number
     *
     * @var int
     */
    protected $max;

    /**
     * Create the rule instance
     *
     * @param integer $min
     * @param integer $max
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
        if (is_numeric($value)) {
            $value = 0 + $value;
        } elseif (is_array($value)) {
            $value = count($value);
        } else {
            $value = mb_strlen($value);
        }

        return $value >= $this->min && $value <= $this->max;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return [
            'numeric' => ":Attribute must be between {$this->min} and {$this->max}",
            'string'  => ":Attribute must be between {$this->min} and {$this->max} characters",
            'array'   => ":Attribute must have between {$this->min} and {$this->max} items",
        ];
    }
}
