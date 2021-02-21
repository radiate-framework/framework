<?php

namespace Radiate\Validation\Rules;

class IsSize implements Rule
{
    /**
     * The size
     *
     * @var int
     */
    protected $size;

    /**
     * Create the rule instance
     *
     * @param integer $size
     */
    public function __construct(int $size)
    {
        $this->size = $size;
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

        return $value == $this->size;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        return [
            'numeric' => ":Attribute must be {$this->size}",
            'string'  => ":Attribute must be {$this->size} characters",
            'array'   => ":Attribute must contain {$this->size} items",
        ];
    }
}
