<?php

namespace Radiate\Validation\Rules;

class IsMax implements Rule
{
    /**
     * The maximum number
     *
     * @var int
     */
    protected $max;

    /**
     * Create the rule instance
     *
     * @param integer $max
     */
    public function __construct(int $max)
    {
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

        return $value <= $this->max;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return [
            'numeric' => ":Attribute may not be greater than {$this->max}",
            'string'  => ":Attribute may not be greater than {$this->max} characters",
            'array'   => ":Attribute may not have more than {$this->max} items",
        ];
    }
}
