<?php

namespace Radiate\Validation;

use Exception;
use Radiate\Support\Arr;
use Radiate\Support\Facades\Validator as ValidatorFacade;

class ValidationException extends Exception
{
    /**
     * The validator instance.
     *
     * @var \Radiate\Validation\Validator
     */
    public $validator;

    /**
     * Create a new exception instance.
     *
     * @param \Radiate\Validation\Validator $validator
     * @return void
     */
    public function __construct(Validator $validator)
    {
        parent::__construct('The given data was invalid.', 422);

        $this->validator = $validator;
    }

    /**
     * Get the validator errors
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->validator->errors();
    }

    /**
     * Create a new validation exception from a plain array of messages.
     *
     * @param array $messages
     * @return static
     */
    public static function withMessages(array $messages)
    {
        $validator = ValidatorFacade::make([], []);

        foreach ($messages as $key => $value) {
            foreach (Arr::wrap($value) as $message) {
                $validator->addError($key, $message);
            }
        }

        return new static($validator);
    }
}
