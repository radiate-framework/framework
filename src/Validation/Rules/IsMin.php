<?php

namespace Radiate\Validation\Rules;

use Radiate\Validation\Validator;

class IsMin implements Rule
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
        $value = $this->validator->getSize($attribute, $value);

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
