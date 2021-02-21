<?php

namespace Radiate\Validation\Rules;

use Radiate\Validation\Validator;

class IsBetween implements Rule
{
    /**
     * The validator
     *
     * @var \Radiate\Validation\Validator
     */
    protected $validator;

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
        $value = $this->validator->getSize($attribute, $value);

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

    /**
     * Set the validator
     *
     * @param \Radiate\Validation\Validator $validator
     * @return void
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }
}
