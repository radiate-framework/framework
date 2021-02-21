<?php

namespace Radiate\Validation\Rules;

use Radiate\Validation\Validator;

class IsSize implements Rule
{
    /**
     * The validator
     *
     * @var \Radiate\Validation\Validator
     */
    protected $validator;

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
        $value = $this->validator->getSize($attribute, $value);

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
