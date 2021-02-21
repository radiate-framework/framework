<?php

namespace Radiate\Validation\Rules;

use Radiate\Validation\Validator;

class IsMax implements Rule
{
    /**
     * The validator
     *
     * @var \Radiate\Validation\Validator
     */
    protected $validator;

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
        $value = $this->validator->getSize($attribute, $value);

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
