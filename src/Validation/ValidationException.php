<?php

namespace Radiate\Validation;

use Radiate\Foundation\Http\Exceptions\HttpResponseException;
use Radiate\Support\Arr;
use Radiate\Support\Facades\Validator as ValidatorFacade;

class ValidationException extends HttpResponseException
{
    /**
     * The validator instance.
     *
     * @var \Radiate\Validation\Validator
     */
    public $validator;

    /**
     * The validation errors.
     *
     * @var array
     */
    public $errors = [];

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
        $this->errors = $validator->errors();
    }

    /**
     * Set the status header and return the message.
     *
     * @return string
     */
    public function getResponse()
    {
        status_header($this->getCode(), $this->getMessage());

        return json_encode(['errors' => $this->validator->errors()]);
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
